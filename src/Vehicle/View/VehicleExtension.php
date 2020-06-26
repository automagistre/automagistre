<?php

declare(strict_types=1);

namespace App\Vehicle\View;

use App\Shared\Doctrine\Registry;
use App\Vehicle\Entity\Model;
use App\Vehicle\Entity\VehicleId;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class VehicleExtension extends AbstractExtension
{
    private Registry $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'vehicle_case_is_defined',
                fn (VehicleId $id) => null !== $this->registry->getBy(Model::class, ['id' => $id])->caseName
            ),
        ];
    }
}
