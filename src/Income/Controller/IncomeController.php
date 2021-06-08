<?php

declare(strict_types=1);

namespace App\Income\Controller;

use App\Customer\Entity\CustomerTransaction;
use App\Customer\Entity\CustomerTransactionId;
use App\Customer\Enum\CustomerTransactionSource;
use App\EasyAdmin\Controller\AbstractController;
use App\Form\Type\MoneyType;
use App\Income\Entity\Income;
use App\Income\Entity\IncomeId;
use App\Income\Entity\IncomePart;
use App\Income\Entity\IncomePartId;
use App\Income\Form\IncomeDto;
use App\Income\Form\PayDto;
use App\Income\Form\Supply\ItemDto;
use App\Income\Form\Supply\ItemType;
use App\Part\Entity\Part;
use App\Part\Entity\SupplyView;
use App\Wallet\Entity\WalletTransaction;
use App\Wallet\Entity\WalletTransactionId;
use App\Wallet\Enum\WalletTransactionSource;
use App\Wallet\Form\WalletType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use LogicException;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use function array_key_exists;
use function assert;
use function in_array;
use function urlencode;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class IncomeController extends AbstractController
{
    public function partAction(): Response
    {
        $incomePartId = $this->request->query->get('income_part_id');

        /** @var IncomePart $incomePart */
        $incomePart = $this->registry->findOneBy(IncomePart::class, ['id' => $incomePartId]);

        return $this->redirectToEasyPath('Income', 'show', [
            'id' => $incomePart->income->toId()->toString(),
            'referer' => $this->request->query->get('referer'),
        ]);
    }

    public function payAction(): Response
    {
        $request = $this->request;

        $income = $this->findEntity(Income::class);

        if (!$income instanceof Income) {
            throw new BadRequestHttpException('Income is required');
        }

        $model = new PayDto();
        $model->money = $income->getTotalPrice();

        $form = $this->createFormBuilder($model)
            ->add('money', MoneyType::class)
            ->add('walletId', WalletType::class, [
                'label' => 'Списать сумму со счёта',
                'required' => true,
            ])
            ->getForm()
            ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $this->registry->manager(Income::class)
                ->transactional(static function (EntityManagerInterface $em) use ($model, $income): void {
                    $customerTransactionId = CustomerTransactionId::generate();

                    $em->persist(
                        new CustomerTransaction(
                            $customerTransactionId,
                            $income->getSupplierId(),
                            $model->money->negative(),
                            CustomerTransactionSource::incomePayment(),
                            $income->toId()->toUuid(),
                            null,
                        ),
                    );

                    $em->persist(
                        new WalletTransaction(
                            WalletTransactionId::generate(),
                            $model->walletId,
                            $model->money->negative(),
                            WalletTransactionSource::incomePayment(),
                            $income->toId()->toUuid(),
                            null,
                        ),
                    );
                })
            ;

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/income/pay.html.twig', [
            'income' => $income,
            'form' => $form->createView(),
        ]);
    }

    public function accrueAction(): Response
    {
        $income = $this->findEntity(Income::class);

        if (!$income instanceof Income) {
            throw new LogicException('Income required.');
        }

        if (!$income->isEditable()) {
            $this->addFlash('error', 'Приход уже оприходован');

            return $this->redirectToReferrer();
        }

        $form = $this->createFormBuilder()
            ->getForm()
            ->handleRequest($this->request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->em;

            $income->accrue($this->getUser());
            $em->persist(
                new CustomerTransaction(
                    CustomerTransactionId::generate(),
                    $income->getSupplierId(),
                    $income->getTotalPrice(),
                    CustomerTransactionSource::incomeDebit(),
                    $income->toId()->toUuid(),
                    null,
                ),
            );

            $em->flush();

            return $this->redirectToReferrer();
        }

        return $this->render('easy_admin/income/accrue.html.twig', [
            'income' => $income,
            'form' => $form->createView(),
        ]);
    }

    public function supplyAction(): Response
    {
        $income = $this->findEntity(Income::class) ?? throw new LogicException('Income required.');

        if (!$income->isEditable()) {
            $this->addFlash('error', 'Приход уже оприходован');

            return $this->redirectToReferrer();
        }

        /** @var SupplyView[] $supplies */
        $supplies = $this->registry->findBy(SupplyView::class, ['supplierId' => $income->getSupplierId()], [
            'partId' => 'ASC',
        ]);

        $existing = $this->registry->manager()
            ->createQueryBuilder()
            ->select('t.partId')
            ->from(IncomePart::class, 't', 't.partId')
            ->where('t.income  = :income')
            ->getQuery()
            ->setParameter('income', $income)
            ->getResult()
        ;

        $items = [];
        foreach ($supplies as $item) {
            if (array_key_exists($item->partId->toString(), $existing)) {
                continue;
            }

            $items[$item->partId->toString()] = new ItemDto($item->partId);
        }

        $form = $this->createFormBuilder(['items' => $items])
            ->add('items', CollectionType::class, [
                'entry_type' => ItemType::class,
            ])
            ->getForm()
            ->handleRequest($this->request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->em;

            foreach ($items as $item) {
                if (0 === $item->quantity) {
                    continue;
                }

                $em->persist(
                    new IncomePart(
                        IncomePartId::generate(),
                        $income,
                        $item->partId,
                        $item->price,
                        $item->quantity,
                    ),
                );
            }

            $em->flush();

            return $this->redirectToEasyPath('Income', 'show', [
                'id' => $income->toId()->toString(),
                'referer' => false,
            ]);
        }

        return $this->render('easy_admin/income/supply.html.twig', [
            'incomeId' => $income->toId(),
            'supplierId' => $income->getSupplierId(),
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
        return new IncomeDto();
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
            $dto->document,
        );

        parent::persistEntity($entity);

        $this->setReferer($this->generateEasyPath('IncomePart', 'new', [
            'income_id' => $incomeId->toString(),
            'referer' => urlencode($this->generateEasyPath('Income', 'show', ['id' => $entity->toId()->toString()])),
        ]));

        return $entity;
    }

    protected function createListQueryBuilder(
        $entityClass,
        $sortDirection,
        $sortField = null,
        $dqlFilter = null,
    ): QueryBuilder {
        $qb = parent::createListQueryBuilder($entityClass, $sortDirection, $sortField, $dqlFilter);

        $part = $this->findEntity(Part::class);

        if ($part instanceof Part) {
            $qb
                ->join('entity.incomeParts', 'income_parts')
                ->where(':part = income_parts.partId')
                ->setParameter('part', $part->toId())
            ;
        }

        return $qb;
    }

    protected function renderTemplate($actionName, $templatePath, array $parameters = []): Response
    {
        if ('list' === $actionName) {
            $parameters['part'] = $this->findEntity(Part::class);
        }

        return parent::renderTemplate($actionName, $templatePath, $parameters);
    }
}
