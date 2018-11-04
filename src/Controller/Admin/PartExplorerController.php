<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class PartExplorerController extends Controller
{
    /**
     * @Route("/part-explorer/{partnumber}", name="part_explorer")
     */
    public function __invoke(Request $request, string $partnumber): Response
    {
        return $this->render('admin/part_explorer.html.twig', [
            'partnumber' => $partnumber,
            'referer' => $request->query->has('referer') ? \urldecode($request->query->get('referer')) : null,
        ]);
    }
}
