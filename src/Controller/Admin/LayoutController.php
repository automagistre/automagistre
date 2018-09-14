<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\EasyAdmin\OrderController;
use App\Entity\Operand;
use App\Manager\PaymentManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class LayoutController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function balance(PaymentManager $paymentManager): Response
    {
        $em = $this->em;

        /** @var Operand $cassa */
        $cassa = $em->getReference(Operand::class, OrderController::COSTIL_CASSA);

        return $this->render('admin/layout/balance.html.twig', [
            'url' => $this->generateUrl('easyadmin', [
                'entity' => 'Operand',
                'action' => 'show',
                'id' => $cassa->getId(),
            ]),
            'money' => $paymentManager->balance($cassa),
        ]);
    }
}
