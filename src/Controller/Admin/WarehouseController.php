<?php

namespace App\Controller\Admin;

use App\Entity\Motion;
use App\Entity\Part;
use App\Model\WarehousePart;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class WarehouseController extends Controller
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/warehouse", name="warehouse")
     */
    public function indexAction(Request $request)
    {
        $qb = $this->em->getRepository(Part::class)->createQueryBuilder('part')
            ->addSelect('SUM(motion.quantity) AS quantity')
            ->leftJoin(Motion::class, 'motion', Join::WITH, 'part.id = motion.part')
            ->groupBy('part.id')
            ->having('SUM(motion.quantity) <> 0');

        $paginator = $this->get('easyadmin.paginator')->createOrmPaginator($qb, $request->query->getInt('page', 1), 20);

        $parts = array_map(function (array $data) {
            return new WarehousePart([
                'part'     => $data[0],
                'quantity' => $data['quantity'],
            ]);
        }, (array) $paginator->getCurrentPageResults());

        return $this->render('easy_admin/warehouse/list.html.twig', [
            'parts' => $parts,
        ]);
    }
}
