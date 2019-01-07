<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Income;
use App\Entity\Wallet;
use App\Events;
use App\Manager\PaymentManager;
use Doctrine\ORM\EntityRepository;
use LogicException;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\GenericEvent;
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
            ->add('wallet', EntityType::class, [
                'label' => 'Списать сумму со счёта',
                'placeholder' => 'Не списывать',
                'required' => false,
                'class' => Wallet::class,
                'query_builder' => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('entity')
                        ->where('entity.useInIncome = TRUE');
                },
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
