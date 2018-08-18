<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Income;
use App\Entity\IncomePart;
use App\Entity\MotionIncome;
use App\Entity\Supply;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use LogicException;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class IncomeController extends AbstractController
{
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
            $this->addFlash('warning', sprintf('Для поставщика "%s" нет Поставок.', $supplier));

            return $this->redirectToReferrer();
        }

        $form = $this->createFormBuilder()
            ->add('supply', ChoiceType::class, [
                'label' => sprintf('Выберите ожидающиеся поставки от "%s"', $supplier),
                'multiple' => true,
                'expanded' => true,
                'choice_loader' => new CallbackChoiceLoader(function () use ($supplies) {
                    return $supplies;
                }),
                'choice_label' => function (Supply $supply) {
                    return sprintf('%s - %s (%s)', $supply->getPart(), $supply->getQuantity() / 100, $this->formatMoney($supply->getPrice()));
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
            $this->addFlash('error', sprintf('Приход "%s" уже оприходван', $income));

            return $this->redirectToReferrer();
        }

        $form = $this->createFormBuilder()->getForm()->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->em;

            $em->transactional(function (EntityManagerInterface $em) use ($income): void {
                $user = $this->getUser();

                foreach ($income->getIncomeParts() as $incomePart) {
                    $quantity = $incomePart->getQuantity();
                    $em->persist(new MotionIncome($incomePart));

                    $supply = $incomePart->getSupply();
                    if ($supply instanceof Supply) {
                        $difference = $supply->getQuantity() - $quantity;

                        if (0 >= $difference) {
                            $supply->receive($user);
                        } else {
                            $em->persist(
                                new Supply($supply->getSupplier(), $supply->getPart(), $supply->getPrice(), $difference)
                            );

                            $supply->receive($user, $incomePart->getQuantity());
                        }
                    }
                }

                $income->accrue($user);
            });

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
     * @param Income $entity
     */
    protected function persistEntity($entity): void
    {
        parent::persistEntity($entity);

        $supplier = $entity->getSupplier();

        $supplies = $this->em->getRepository(Supply::class)->findOneBy(['supplier' => $supplier, 'receivedAt' => null]);
        if (null === $supplies) {
            $this->setReferer($this->generateEasyPath('IncomePart', 'new', ['income_id' => $entity->getId()]));
        } else {
            $this->setReferer($this->generateEasyPath($entity, 'supply', ['income_id' => $entity->getId()]));
        }
    }

    /**
     * @param Income $entity
     */
    protected function updateEntity($entity): void
    {
        parent::updateEntity($entity);

        $this->setReferer($this->generateEasyPath($entity, 'show'));
    }
}
