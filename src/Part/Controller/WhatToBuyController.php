<?php

declare(strict_types=1);

namespace App\Part\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Part\Entity\PartView;

final class WhatToBuyController extends AbstractController
{
    public function listAction(): \Symfony\Component\HttpFoundation\Response
    {
        $parts = $this->registry->manager()->createQueryBuilder()
            ->select('t')
            ->from(PartView::class, 't')
            ->where('t.quantity - t.ordered < t.orderFromQuantity')
            ->getQuery()
            ->getResult();

        return $this->render('easy_admin/part/what_to_buy.html.twig', [
            'parts' => $parts,
        ]);
    }
}
