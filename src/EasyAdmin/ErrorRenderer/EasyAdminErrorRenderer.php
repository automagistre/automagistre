<?php

declare(strict_types=1);

namespace App\EasyAdmin\ErrorRenderer;

use Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\Security\Core\Security;
use Throwable;
use Twig\Environment;

final class EasyAdminErrorRenderer implements ErrorRendererInterface
{
    public function __construct(
        private ErrorRendererInterface $fallbackErrorRenderer,
        private Security $security,
        private Environment $twig,
        private bool $debug,
    ) {
    }

    public function render(Throwable $exception): FlattenException
    {
        $flatten = $this->fallbackErrorRenderer->render($exception);

        if ($this->debug) {
            return $flatten;
        }

        if (null === $this->security->getToken()) {
            return $flatten;
        }

        return $flatten->setAsString(
            $this->twig->render('easy_admin/error.html.twig', [
                'status_code' => $flatten->getStatusCode(),
                'status_text' => $flatten->getMessage(),
            ]),
        );
    }
}
