<?php

declare(strict_types=1);

namespace App\Controller;

use App\Roles;
use Symfony\Bundle\TwigBundle\Controller\ExceptionController as TwigExceptionController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Environment;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class ExceptionController extends TwigExceptionController
{
    private const FIREWALL_MAP = [
        'security.firewall.map.context.admin' => 'admin',
        'security.firewall.map.context.www' => 'www',
    ];

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(
        Environment $twig,
        bool $debug,
        RequestStack $requestStack,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        parent::__construct($twig, $debug);

        $this->requestStack = $requestStack;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * {@inheritdoc}
     */
    protected function findTemplate(Request $request, $format, $code, $showException): string
    {
        if ($this->debug && $showException) {
            return parent::findTemplate($request, $format, $code, $showException);
        }

        $masterRequest = $this->requestStack->getMasterRequest();
        $firewallContext = $masterRequest instanceof Request
            ? $masterRequest->attributes->get('_firewall_context')
            : null;

        $zone = null;
        if (null !== $firewallContext) {
            $zone = self::FIREWALL_MAP[$firewallContext];
        }

        if (null === $zone) {
            $host = $request->getHost();

            if (0 === \strpos($host, 'www')) {
                $zone = 'www';
            } elseif (0 === \strpos($host, 'sto')) {
                $zone = 'admin';
            }
        }

        $tokenStorage = $this->tokenStorage;
        $authorizationChecker = $this->authorizationChecker;
        if (
            'admin' === $zone
            && (null === $tokenStorage->getToken() || !$authorizationChecker->isGranted(Roles::EMPLOYEE))
        ) {
            return 'www/error.html.twig';
        }

        switch ($zone) {
            case 'admin':
                return 'easy_admin/error.html.twig';
            case 'www':
                return 'www/error.html.twig';
            default:
                return parent::findTemplate($request, $format, $code, $showException);
        }
    }
}
