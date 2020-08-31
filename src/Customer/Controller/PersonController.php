<?php

declare(strict_types=1);

namespace App\Customer\Controller;

use App\Customer\Entity\OperandId;
use App\Customer\Entity\Person;
use App\Customer\Event\PersonCreated;
use App\Customer\Form\PersonDto;
use App\Customer\Form\PersonType;
use function array_map;
use function assert;
use Doctrine\ORM\QueryBuilder;
use function explode;
use function mb_strtolower;
use function sprintf;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PersonController extends OperandController
{
    public function widgetAction(): Response
    {
        $request = $this->request;
        $em = $this->em;

        /** @var PersonDto $dto */
        $dto = $this->createWithoutConstructor(PersonDto::class);

        $form = $this->createForm(PersonType::class, $dto)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $id = OperandId::generate();

            $person = new Person(
                $id,
            );
            $person->setFirstname($dto->firstName);
            $person->setLastname($dto->lastName);
            $person->setEmail($dto->email);
            $person->setTelephone($dto->telephone);

            $em->persist($person);
            $em->flush();

            return new JsonResponse([
                'id' => $id->toString(),
                'text' => $this->display($id),
            ]);
        }

        if (null !== $dto->telephone && $form->isSubmitted()) {
            /** @var Person|null $person */
            $person = $em->createQueryBuilder()
                ->select('t')
                ->from(Person::class, 't')
                ->where('t.telephone = :telephone')
                ->getQuery()
                ->setParameter('telephone', $dto->telephone, 'phone_number')
                ->getOneOrNullResult();

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
            ])
        );

        $this->event(new PersonCreated($entity));
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
        $dqlFilter = null
    ): QueryBuilder {
        $qb = $this->em->getRepository(Person::class)->createQueryBuilder('person');

        foreach (explode(' ', $searchQuery) as $key => $item) {
            $key = ':search_'.$key;

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('LOWER(person.firstname)', $key),
                $qb->expr()->like('LOWER(person.lastname)', $key),
                $qb->expr()->like('LOWER(person.telephone)', $key),
                $qb->expr()->like('LOWER(person.email)', $key)
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

        $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $query->getInt('page', 1));

        $data = array_map(function (Person $person): array {
            $formattedTelephone = $this->formatTelephone($person->getTelephone() ?? $person->getOfficePhone());

            return [
                'id' => $person->toId()->toString(),
                'text' => sprintf('%s %s', $person->getFullName(), $formattedTelephone),
                'firstName' => $person->getFirstname(),
                'lastName' => $person->getLastname(),
                'phone' => $formattedTelephone,
            ];
        }, (array) $paginator->getCurrentPageResults());

        return new JsonResponse(['results' => $data]);
    }
}
