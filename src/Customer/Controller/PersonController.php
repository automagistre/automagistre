<?php

declare(strict_types=1);

namespace App\Customer\Controller;

use App\Customer\Entity\OperandId;
use App\Customer\Entity\Person;
use App\Customer\Form\PersonDto;
use App\Customer\Form\PersonType;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Search\Paginator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function array_map;
use function assert;
use function explode;
use function mb_strtolower;
use function sprintf;
use function in_array;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class PersonController extends OperandController
{
    protected function initialize(Request $request): void
    {
        parent::initialize($request);

        if (in_array($request->query->get('action'), ['edit', 'delete'], true)) {
            $easyadmin = $request->attributes->get('easyadmin');
            $easyadmin['item'] = $this->registry->get(Person::class, $request->query->get('id'));

            $request->attributes->set('easyadmin', $easyadmin);
        }
    }

    public function widgetAction(): Response
    {
        $request = $this->request;
        $em = $this->em;

        $dto = new PersonDto();

        $form = $this->createForm(PersonType::class, $dto)
            ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $id = OperandId::generate();

            $person = new Person(
                $id,
            );
            $person->firstname = $dto->firstName;
            $person->lastname = $dto->lastName;
            $person->email = $dto->email;
            $person->telephone = $dto->telephone;

            $em->persist($person);
            $em->flush();

            return new JsonResponse([
                'id' => $id->toString(),
                'text' => $this->display($id),
            ]);
        }

        if (null !== $dto->telephone && $form->isSubmitted()) {
            /** @var null|Person $person */
            $person = $em->createQueryBuilder()
                ->select('t')
                ->from(Person::class, 't')
                ->where('t.telephone = :telephone')
                ->getQuery()
                ->setParameter('telephone', $dto->telephone, 'phone_number')
                ->getOneOrNullResult()
            ;

            if (null !== $person) {
                return new JsonResponse([
                    'id' => $person->toId()->toString(),
                    'text' => $this->display($person->toId()),
                ]);
            }
        }

        return $this->render('easy_admin/widget.html.twig', [
            'id' => 'person',
            'label' => 'Новый клиент (ФЛ)',
            'form' => $form->createView(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function createNewEntity(): Person
    {
        return new Person(OperandId::generate());
    }

    /**
     * {@inheritdoc}
     */
    protected function persistEntity($entity): void
    {
        assert($entity instanceof Person);

        parent::persistEntity($entity);

        $this->setReferer(
            $this->generateEasyPath('Person', 'show', [
                'id' => $entity->toId()->toString(),
            ]),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function createSearchQueryBuilder(
        $entityClass,
        $searchQuery,
        array $searchableFields,
        $sortField = null,
        $sortDirection = null,
        $dqlFilter = null,
    ): QueryBuilder {
        $qb = $this->em->getRepository(Person::class)->createQueryBuilder('person');

        foreach (explode(' ', $searchQuery) as $key => $item) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('LOWER(person.firstname)', $key),
                $qb->expr()->like('LOWER(person.lastname)', $key),
                $qb->expr()->like('LOWER(person.telephone)', $key),
                $qb->expr()->like('LOWER(person.email)', $key),
            ));

            $qb->setParameter($key, '%'.mb_strtolower($item).'%');
        }

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    protected function autocompleteAction(): JsonResponse
    {
        $query = $this->request->query;

        $qb = $this->createSearchQueryBuilder((string) $query->get('entity'), (string) $query->get('query'), []);

        $paginator = $this->get(Paginator::class)->createOrmPaginator($qb, $query->getInt('page', 1));

        $data = array_map(function (Person $person): array {
            $formattedTelephone = $this->formatTelephone($person->telephone ?? $person->officePhone);

            return [
                'id' => $person->toId()->toString(),
                'text' => sprintf('%s %s', $person->getFullName(), $formattedTelephone),
                'firstName' => $person->firstname,
                'lastName' => $person->lastname,
                'phone' => $formattedTelephone,
            ];
        }, (array) $paginator->getCurrentPageResults());

        return new JsonResponse(['results' => $data]);
    }
}
