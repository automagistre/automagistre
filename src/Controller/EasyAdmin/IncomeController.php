<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Income;
use App\Entity\IncomePart;
use App\Entity\Supply;
use App\Entity\Wallet;
use App\Events;
use App\Form\Type\WalletType;
use App\Manager\PaymentManager;
use Doctrine\ORM\Query\Expr\Join;
use LogicException;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class IncomeController extends AbstractController
{
    /**
     * @var PaymentManager
     */
    private $paymentManager;

    public function __construct(PaymentManager $paymentManager)
    {
        $this->paymentManager = $paymentManager;
    }

    public function supplyAction(): Response
    {
        $income = $this->getEntity(Income::class);
        if (!$income instanceof Income) {
            throw new LogicException('Income required.');
        }

        $em = $this->em;
        $supplier = $income->getSupplier();
        $supplies = $em->getRepository(Supply::class)
            ->createQueryBuilder('entity')
            ->select('entity')
            ->leftJoin(
                IncomePart::class,
                'income_part',
                Join::WITH,
                'income_part.supply = entity')
            ->where('entity.receivedAt IS NULL')
            ->andWhere('income_part.supply IS NULL')
            ->orderBy('entity.id', 'ASC')
            ->getQuery()
            ->getResult();

        if ([] === $supplies) {
            $this->addFlash('warning', \sprintf('Для поставщика "%s" нет Поставок.', $supplier));

            return $this->redirectToReferrer();
        }

        $form = $this->createFormBuilder()
            ->add('supply', ChoiceType::class, [
                'label' => \sprintf('Выберите ожидающиеся поставки от "%s"', $supplier),
                'multiple' => true,
                'expanded' => true,
                'choice_loader' => new CallbackChoiceLoader(function () use ($supplies) {
                    return $supplies;
                }),
                'choice_label' => function (Supply $supply) {
                    return \sprintf('%s - %s (%s)', $supply->getPart(), $supply->getQuantity() / 100, $this->formatMoney($supply->getPrice()));
                },
                'choice_value' => 'id',
                'choice_name' => 'id',
            ])
            ->getForm()
            ->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form->get('supply')->getData() as $supply) {
                /** @var Supply $supply */
                $incomePart = IncomePart::fromSupply($supply);
                $income->addIncomePart($incomePart);

                $em->persist($incomePart);
            }

            $em->flush();

            return $this->redirectToEasyPath($income, 'show');
        }

        return $this->render('easy_admin/income/supply.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function accrueAction(): Response
    {
        $income = $this->getEntity(Income::class);
        if (!$income instanceof Income) {
            throw new LogicException('Income required.');
        }

        if (!$income->isEditable()) {
            $this->addFlash('error', \sprintf('Приход "%s" уже оприходван', $income));

            return $this->redirectToReferrer();
        }

        $form = $this->createFormBuilder()
            ->add('wallet', WalletType::class, [
                'label' => 'Списать сумму со счёта',
                'placeholder' => 'Не списывать',
                'required' => false,
            ])
            ->getForm()
            ->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->em;

            $em->transactional(function () use ($form, $income): void {
                $income->accrue($this->getUser());

                $wallet = $form->get('wallet')->getData();
                if ($wallet instanceof Wallet) {
                    $this->paymentManager->createPayment(
                        $wallet,
                        \sprintf('# Списание по поступлению #%s', $income->getId()),
                        $income->getTotalPrice()->negative()
                    );
                }
            });

            $this->event(Events::INCOME_ACCRUED, new GenericEvent($income));

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/income/accrue.html.twig', [
            'income' => $income,
            'form' => $form->createView(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function isActionAllowed($actionName): bool
    {
        if (\in_array($actionName, ['edit', 'delete'], true)) {
            $income = $this->findCurrentEntity();

            if (!$income instanceof Income) {
                throw new LogicException('Income required.');
            }

            if (!$income->isEditable()) {
                return false;
            }
        }

        return parent::isActionAllowed($actionName);
    }

    /**
     * @param Income $entity
     */
    protected function persistEntity($entity): void
    {
        parent::persistEntity($entity);

        $this->setReferer($this->generateEasyPath('IncomePart', 'new', [
            'income_id' => $entity->getId(),
            'referer' => \urlencode($this->generateEasyPath($entity, 'show')),
        ]));
    }
}
