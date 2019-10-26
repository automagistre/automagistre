<?php

declare(strict_types=1);

namespace App\Controller\WWW\Service;

use App\Entity\Landlord\Review;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Exception\OutOfRangeCurrentPageException;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ReviewsController extends AbstractController
{
    private const REVIEWS_PER_PAGE = 9;

    /**
     * @Route("/reviews", name="reviews")
     *
     * @Cache(vary={"X-Requested-With"})
     */
    public function __invoke(Request $request, EntityManagerInterface $em): Response
    {
        $page = $request->query->getInt('page', 1);
        $manufacturer = $request->query->getAlnum('manufacturer', 'all');

        $qb = $em->createQueryBuilder()
            ->select('entity')
            ->from(Review::class, 'entity');

        if ('all' !== $manufacturer) {
            $qb->where('entity.manufacturer = :manufacturer')
                ->setParameter('manufacturer', $manufacturer);
        }

        try {
            $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($qb));
            $pagerfanta->setMaxPerPage(self::REVIEWS_PER_PAGE);
            $pagerfanta->setCurrentPage($page);

            $reviews = $pagerfanta->getCurrentPageResults();
        } catch (OutOfRangeCurrentPageException $e) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('www/Service/Reviews/reviews.html.twig', [
                'reviews' => $reviews,
            ]);
        }

        return $this->render('www/Service/Reviews/index.html.twig', [
            'reviews' => $reviews,
            'manufacturer' => $manufacturer,
            'reviewsCount' => $this->reviewsCount($em),
        ]);
    }

    public function section(Request $request, EntityManagerInterface $em): Response
    {
        $brand = $request->attributes->getAlnum('brand');
        $reviews = $em->createQueryBuilder()
            ->select('entity')
            ->from(Review::class, 'entity')
            ->where('entity.manufacturer = :brand')
            ->setMaxResults(3 + 5)
            ->getQuery()
            ->setParameter('brand', $brand)
            ->getResult();

        if ([] === $reviews) {
            return new Response();
        }

        return $this->render('www/Service/Reviews/section.html.twig', [
            'reviews' => $reviews,
            'brand' => $brand,
            'reviewsCount' => $this->reviewsCount($em),
        ]);
    }

    private function reviewsCount(EntityManagerInterface $em): int
    {
        return (int) $em->createQueryBuilder()
            ->select('COUNT(entity.id)')
            ->from(Review::class, 'entity')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
