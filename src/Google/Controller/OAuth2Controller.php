<?php

declare(strict_types=1);

namespace App\Google\Controller;

use App\EasyAdmin\Controller\AbstractController;
use App\Google\Entity\Token;
use App\Shared\Doctrine\Registry;
use function array_key_exists;
use Google_Client;
use function is_string;
use const JSON_UNESCAPED_UNICODE;
use Sentry\Util\JSON;
use Symfony\Component\HttpFoundation\Request;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class OAuth2Controller extends AbstractController
{
    private Google_Client $googleClient;

    public function __construct(Google_Client $googleClient, Registry $registry)
    {
        $this->googleClient = $googleClient;
        $this->registry = $registry;
    }

    public function indexAction(Request $request)
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
            );
        } else {
            $this->addFlash('error', 'Google return: '.JSON::encode($payload, JSON_UNESCAPED_UNICODE));
        }

        return $this->redirectToEasyPath('Review', 'list');
    }
}
