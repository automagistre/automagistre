<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Doctrine\Registry;
use App\Entity\Landlord\Part;
use App\Entity\Tenant\Income;
use App\Entity\Tenant\Wallet;
use App\Event\IncomeAccrued;
use App\Form\Type\MoneyType;
use App\Manager\PaymentManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use LogicException;
use Money\Money;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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

    public function payAction(): Response
    {
        $request = $this->request;

        $income = $this->getEntity(Income::class);
        if (!$income instanceof Income) {
            throw new BadRequestHttpException('Income is required');
        }

        $model = new \stdClass();
        $model->money = $income->getTotalPrice();
        $model->wallet = null;

        $form = $this->createFormBuilder($model)
            ->add('money', MoneyType::class)
            ->add('wallet', EntityType::class, [
                'label' => 'Списать сумму со счёта',
                'required' => true,
                'class' => Wallet::class,
                'query_builder' => static function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('entity')
                        ->where('entity.useInIncome = TRUE');
                },
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $registry = $this->container->get(Registry::class);

            $registry->manager(Income::class)
                ->transactional(function () use ($model, $income): void {
                    $description = \sprintf('# Оплата за поставку #%s', $income->getId());

                    /** @var Money $money */
                    $money = $model->money;
                    $money = $money->negative();

                    $this->paymentManager->createPayment($income->getSupplier(), $description, $money);
                    $this->paymentManager->createPayment($model->wallet, $description, $money);
                });

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/income/pay.html.twig', [
            'income' => $income,
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
            $this->addFlash('error', \sprintf('Приход "%s" уже оприходван', (string) $income));

            return $this->redirectToReferrer();
        }

        $form = $this->createFormBuilder()
            ->getForm()
            ->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->em;

            $em->transactional(function () use ($income): void {
                $income->accrue($this->getUser());

                $description = \sprintf('# Начисление по поставке №%s', $income->getId());

                $this->paymentManager->createPayment($income->getSupplier(), $description, $income->getTotalPrice());
            });

            $this->event(new IncomeAccrued($income));

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
     * {@inheritdoc}
     */
    protected function persistEntity($entity): void
    {
        \assert($entity instanceof Income);

        parent::persistEntity($entity);

        $this->setReferer($this->generateEasyPath('IncomePart', 'new', [
            'income_id' => $entity->getId(),
            'referer' => \urlencode($this->generateEasyPath($entity, 'show')),
        ]));
    }

    protected function createListQueryBuilder(
        $entityClass,
        $sortDirection,
        $sortField = null,
        $dqlFilter = null
    ): QueryBuilder {
        $qb = parent::createListQueryBuilder($entityClass, $sortDirection, $sortField, $dqlFilter);

        $part = $this->getEntity(Part::class);
        if ($part instanceof Part) {
            $qb
                ->join('entity.incomeParts', 'income_parts')
                ->where(':part = income_parts.part.id')
                ->setParameter('part', $part->getId());
        }

        return $qb;
    }

    protected function renderTemplate($actionName, $templatePath, array $parameters = []): Response
    {
        if ('list' === $actionName) {
            $parameters['part'] = $this->getEntity(Part::class);
        }

        return parent::renderTemplate($actionName, $templatePath, $parameters);
    }
}
