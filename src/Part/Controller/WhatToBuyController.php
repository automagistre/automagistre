<?php

declare(strict_types=1);

namespace App\Part\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Part\Entity\PartView;
use App\Part\Enum\WhatToBuyStatus;
use function array_map;
use Doctrine\ORM\Query\Expr\Andx;
use Symfony\Component\HttpFoundation\Response;
use function usort;

final class WhatToBuyController extends AbstractController
{
    public function listAction(): Response
    {
        $parts = $this->registry->manager()->createQueryBuilder()
            ->select('t')
            ->from(PartView::class, 't')
            ->where('(t.ordered - t.quantity - t.suppliesQuantity) > 0') // (toBuy) >
            ->orWhere(
                new Andx([
                    't.orderFromQuantity > 0',
                    '(t.quantity - t.ordered + t.suppliesQuantity) <= t.orderFromQuantity', // (leftInStock) <
                ])
            )
            ->orWhere('t.suppliesQuantity > 0')
            ->getQuery()
            ->getResult();

        $parts = array_map(static function (PartView $view): array {
            $toBuy = $view->ordered - $view->quantity - $view->suppliesQuantity;
            $leftInStock = $view->quantity - $view->ordered + $view->suppliesQuantity;

            if (($leftInStock + $toBuy) < $view->orderUpToQuantity) {
                $toBuy = $view->orderUpToQuantity - $leftInStock;
            }

            $status = null;
            if ($view->quantity < 0) {
                $status = WhatToBuyStatus::subzeroQuantity();
            } elseif (0 >= $toBuy) {
                $status = WhatToBuyStatus::ok();
            } elseif (($view->quantity - $view->ordered) < 0) {
                $status = WhatToBuyStatus::needSupplyForOrder();
            } elseif ($leftInStock < $view->orderUpToQuantity) {
                $status = WhatToBuyStatus::needSupplyForStock();
            }

            return [
                'view' => $view,
                'toBuy' => $toBuy > 0 ? $toBuy : 0,
                'status' => $status,
            ];
        }, $parts);

        usort(
            $parts,
            fn (array $left, array $right) => $left['status']->to('sort') <=> $right['status']->to('sort'),
        );

        return $this->render('easy_admin/part/what_to_buy.html.twig', [
            'parts' => $parts,
        ]);
    }
}
