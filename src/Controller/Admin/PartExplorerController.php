<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartExplorerController extends Controller
{
    /**
     * @Route("/part-explorer/{partnumber}", name="part_explorer")
     */
    public function __invoke(string $partnumber): Response
    {
        return $this->render('admin/part_explorer.html.twig', [
            'partnumber' => $partnumber,
        ]);
    }
}
