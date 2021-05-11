<?php

declare(strict_types=1);

namespace App\Review\Google\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Review\Google\Entity\Token;
use App\Shared\Doctrine\Registry;
use Google_Client;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use function array_key_exists;
use function is_string;
use function json_encode;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_UNICODE;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class OAuth2Controller extends AbstractController
{
    public function __construct(private Google_Client $googleClient, Registry $registry)
    {
        $this->registry = $registry;
    }

    public function indexAction(Request $request): RedirectResponse
    {
        $code = $request->query->get('code');

        if (!is_string($code)) {
            return $this->redirect($this->googleClient->createAuthUrl());
        }

        $payload = $this->googleClient->fetchAccessTokenWithAuthCode($code);

        if (array_key_exists('access_token', $payload)) {
            $this->registry->add(
                Token::create(
                    $payload,
                ),
            )->flush();
        } else {
            $this->addFlash('error', 'Google return: '.json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
        }

        return $this->redirectToEasyPath('Review', 'list');
    }
}
