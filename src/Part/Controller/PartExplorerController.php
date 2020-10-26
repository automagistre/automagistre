<?php

declare(strict_types=1);

namespace App\Part\Controller;

use App\EasyAdmin\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function urldecode;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class PartExplorerController extends AbstractController
{
    public function indexAction(Request $request): Response
    {
        $partnumber = (string) $request->query->get('partnumber');

        return $this->render('admin/part_explorer.html.twig', [
            'partnumber' => $partnumber,
            'referer' => $request->query->has('referer') ? urldecode($request->query->get('referer')) : null,
        ]);
    }
}
