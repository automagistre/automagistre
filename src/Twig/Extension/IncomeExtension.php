<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Entity\Landlord\Operand;
use App\Manager\SupplierManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class IncomeExtension extends AbstractExtension
{
    /**
     * @var SupplierManager
     */
    private $supplierManager;

    public function __construct(SupplierManager $supplierManager)
    {
        $this->supplierManager = $supplierManager;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('supplier_unpaid_income', function (Operand $supplier): array {
                return $this->supplierManager->unpaidIncome($supplier);
            }),
        ];
    }
}
