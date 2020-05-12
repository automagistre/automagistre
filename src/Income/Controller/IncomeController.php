<?php

declare(strict_types=1);

namespace App\Income\Controller;

use App\Controller\EasyAdmin\AbstractController;
use App\Customer\Domain\Operand;
use App\Doctrine\Registry;
use App\Entity\Tenant\Wallet;
use App\Event\IncomeAccrued;
use App\Form\Type\MoneyType;
use App\Income\Entity\Income;
use App\Income\Entity\IncomeId;
use App\Income\Form\IncomeDto;
use App\Manager\PaymentManager;
use App\Part\Domain\Part;
use function assert;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use function in_array;
use LogicException;
use Money\Money;
use function sprintf;
use stdClass;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use function urlencode;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class IncomeController extends AbstractController
{
    private PaymentManager $paymentManager;

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

        $model = new stdClass();
        $model->money = $income->getTotalPrice();
        $model->wallet = null;

        $form = $this->createFormBuilder($model)
            ->add('money', MoneyType::class)
            ->add('wallet', EntityType::class, [
                'label' => 'Списать сумму со счёта',
                'required' => true,
                'class' => Wallet::class,
                'query_builder' => fn (EntityRepository $repository) => $repository->createQueryBuilder('entity')
                    ->where('entity.useInIncome = TRUE'),
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $registry = $this->container->get(Registry::class);

            $registry->manager(Income::class)
                ->transactional(function () use ($model, $income): void {
                    $description = sprintf('# Оплата за поставку #%s', $income->toId()->toString());

                    /** @var Money $money */
                    $money = $model->money;
                    $money = $money->negative();

                    $supplier = $this->container->get(Registry::class)
                        ->findBy(Operand::class, ['uuid' => $income->getSupplierId()]);

                    $this->paymentManager->createPayment($supplier, $description, $money);
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
            $this->addFlash('error', 'Приход уже оприходван');

            return $this->redirectToReferrer();
        }

        $form = $this->createFormBuilder()
            ->getForm()
            ->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->em;

            $em->transactional(function () use ($income): void {
                $income->accrue($this->getUser());

                $description = sprintf('# Начисление по поставке №%s', $income->toId()->toString());

                $supplier = $this->container->get(Registry::class)
                    ->findBy(Operand::class, ['uuid' => $income->getSupplierId()]);
                $this->paymentManager->createPayment($supplier, $description, $income->getTotalPrice());
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
        if (in_array($actionName, ['edit', 'delete'], true)) {
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
    protected function createNewEntity(): IncomeDto
    {
        return $this->createWithoutConstructor(IncomeDto::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): Income
    {
        $dto = $entity;
        assert($dto instanceof IncomeDto);

        $incomeId = IncomeId::generate();
        $entity = new Income(
            $incomeId,
            $dto->supplierId,
            $dto->document
        );

        parent::persistEntity($entity);

        $this->setReferer($this->generateEasyPath('IncomePart', 'new', [
            'income_id' => $incomeId->toString(),
            'referer' => urlencode($this->generateEasyPath($entity, 'show')),
        ]));

        return $entity;
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
                ->where(':part = income_parts.partId')
                ->setParameter('part', $part->toId());
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
