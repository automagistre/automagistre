<?php

declare(strict_types=1);

namespace App\Controller\EasyAdmin;

use App\Entity\Car;
use App\Entity\Operand;
use App\Entity\OperandNote;
use App\Entity\Order;
use App\Entity\Organization;
use App\Entity\Payment;
use App\Entity\Person;
use App\Entity\Wallet;
use App\Manager\PaymentManager;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\Query\Expr\Join;
use Money\Currency;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class OperandController extends AbstractController
{
    /**
     * @var PaymentManager
     */
    private $paymentManager;

    /**
     * @required
     */
    public function setPaymentManager(PaymentManager $paymentManager): void
    {
        $this->paymentManager = $paymentManager;
    }

    /**
     * {@inheritdoc}
     */
    public function indexAction(Request $request): Response
    {
        if (self::class !== static::class || 'autocomplete' === $request->query->get('action')) {
            return parent::indexAction($request);
        }

        $id = $request->query->get('id');
        $entity = $this->getDoctrine()->getManager()->getRepository(Operand::class)->find($id);
        $class = ClassUtils::getClass($entity);
        $config = $this->get('easyadmin.config.manager')->getEntityConfigByClass($class);

        return $this->redirectToRoute('easyadmin', \array_merge($request->query->all(), [
            'entity' => $config['name'],
        ]));
    }

    /**
     * @param Operand $entity
     */
    protected function persistEntity($entity): void
    {
        $entity->setWallet($wallet = new Wallet($entity, 'Основной', new Currency('RUB')));

        $this->em->persist($wallet);

        parent::persistEntity($entity);
    }

    /**
     * {@inheritdoc}
     */
    protected function renderTemplate($actionName, $templatePath, array $parameters = []): Response
    {
        if ('show' === $actionName) {
            $em = $this->em;

            /** @var Operand $operand */
            $operand = $parameters['entity'];

            $parameters['cars'] = $em->getRepository(Car::class)
                ->findBy(['owner' => $operand]);
            $parameters['orders'] = $em->getRepository(Order::class)
                ->findBy(['customer' => $operand], ['closedAt' => 'DESC'], 20);
            $parameters['payments'] = $em->getRepository(Payment::class)
                ->createQueryBuilder('entity')
                ->join('entity.recipient', 'wallet')
                ->where('wallet.owner = :owner')
                ->orderBy('entity.id', 'DESC')
                ->setMaxResults(20)
                ->getQuery()
                ->setParameters(['owner' => $operand])
                ->getResult();

            $parameters['notes'] = $em->getRepository(OperandNote::class)
                ->findBy(['operand' => $operand], ['createdAt' => 'DESC']);
        }

        return parent::renderTemplate($actionName, $templatePath, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    protected function autocompleteAction(): JsonResponse
    {
        $query = $this->request->query;

        $qb = $this->em->getRepository(Operand::class)->createQueryBuilder('operand')
            ->leftJoin(Person::class, 'person', Join::WITH, 'person.id = operand.id AND operand INSTANCE OF '.Person::class)
            ->leftJoin(Organization::class, 'organization', Join::WITH, 'organization.id = operand.id AND operand INSTANCE OF '.Organization::class);

        foreach (\explode(' ', $query->get('query')) as $key => $item) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('person.firstname', $key),
                $qb->expr()->like('person.lastname', $key),
                $qb->expr()->like('person.telephone', $key),
                $qb->expr()->like('person.email', $key),
                $qb->expr()->like('organization.name', $key)
            ));

            $qb->setParameter($key, '%'.$item.'%');
        }

        $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $query->get('page', 1));

        $data = \array_map(function (Operand $entity) {
            $text = $entity->getFullName();

            $telephone = $entity->getTelephone();
            if (null !== $telephone) {
                $text .= \sprintf(' (%s)', $this->formatTelephone($telephone));
            }

            return [
                'id' => $entity->getId(),
                'text' => $text,
            ];
        }, (array) $paginator->getCurrentPageResults());

        return new JsonResponse(['results' => $data]);
    }
}
