<?php

declare(strict_types=1);

namespace App\Supply;

use App\Doctrine\EntityManager;
use App\Entity\Part;
use App\Entity\PartnerOperand;
use App\Entity\PartnerSupplyImport;
use App\Entity\Supply;
use App\Partner\Ixora\Orders;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Importer
{
    use EntityManager;

    /**
     * @var Orders
     */
    private $orders;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(Orders $orders, RegistryInterface $registry, LoggerInterface $logger)
    {
        $this->orders = $orders;
        $this->registry = $registry;
        $this->logger = $logger;
    }

    public function import(DateTime $date): void
    {
        $em = $this->getManager();

        /** @var PartnerOperand $supplier */
        $supplier = $em->getRepository(PartnerOperand::class)
            ->findOneBy(['name' => $this->orders::getSupplierName()])->getOperand();

        foreach ($this->orders->find($date) as $supply) {
            if (null !== $em->getRepository(PartnerSupplyImport::class)->findOneBy(['externalId' => $supply->id])) {
                continue;
            }

            $em->transactional(function (EntityManagerInterface $em) use ($supply, $supplier): void {
                foreach ($supply->items as $supplyItem) {
                    $part = $em->createQueryBuilder()
                        ->select('part')
                        ->from(Part::class, 'part')
                        ->join('part.manufacturer', 'manufacturer')
                        ->where('part.number = :number')
                        ->andWhere('manufacturer.name = :manufacturer')
                        ->setParameters([
                            'number' => $supplyItem->number,
                            'manufacturer' => $supplyItem->manufacturer,
                        ])
                        ->getQuery()
                        ->getOneOrNullResult();

                    if (!$part instanceof Part) {
                        $this->logger->alert(\sprintf(
                            'Orders import failed. Not found Part for manufacturer "%s", number "%s"',
                            $supplyItem->manufacturer,
                            $supplyItem->number
                        ));

                        $em->rollback();

                        return;
                    }

                    $em->persist(
                        new Supply($supplier->getOperand(), $part, $supplyItem->price, $supplyItem->quantity)
                    );
                }

                $em->persist(new PartnerSupplyImport($supply->id, $supply->date));
            });
        }
    }
}
