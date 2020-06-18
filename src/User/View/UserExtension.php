<?php
declare(strict_types=1);

namespace App\User\View;

use App\Customer\Entity\OperandId;
use App\Shared\Doctrine\Registry;
use App\User\Entity\UserId;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class UserExtension extends AbstractExtension
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @inheritDoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('user_to_person', function (UserId $userId): ?OperandId {
                $view = $this->registry->view($userId);

                return $view['personId'];
            }),
        ];
    }
}
