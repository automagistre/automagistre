<?php

declare(strict_types=1);

namespace App\Supply;

use App\Entity\Part;
use App\Entity\PartnerOperand;
use App\Entity\PartnerSupplyImport;
use App\Entity\Supply;
use App\Partner\Ixora\Orders;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Importer
{
    /**
     * @var Orders
     */
    private $orders;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(Orders $orders, EntityManager $em, LoggerInterface $logger)
    {
        $this->orders = $orders;
        $this->em = $em;
        $this->logger = $logger;
    }

    public function import(\DateTime $date): void
    {
        $supplier = $this->em->getRepository(PartnerOperand::class)
            ->findOneBy(['name' => $this->orders::getSupplierName()])->getOperand();

        foreach ($this->orders->find($date) as $supply) {
            if ($this->em->getRepository(PartnerSupplyImport::class)->findOneBy(['externalId' => $supply->id])) {
                continue;
            }

            $this->em->beginTransaction();

            foreach ($supply->items as $supplyItem) {
                $part = $this->em->getRepository(Part::class)->createQueryBuilder('part')
                    ->join('part.manufacturer', 'manufacturer')
                    ->where('part.number = :number')
                    ->andWhere('manufacturer.name = :manufacturer')
                    ->setParameters([
                        'number' => $supplyItem->number,
                        'manufacturer' => $supplyItem->manufacturer,
                    ])
                    ->getQuery()
                    ->getOneOrNullResult();

                if (!$part) {
                    $this->logger->alert(sprintf(
                        'Orders import failed. Not found Part for manufacturer "%s", number "%s"',
                        $supplyItem->manufacturer,
                        $supplyItem->number
                    ));

                    $this->em->rollback();

                    continue 2;
                }

                $this->em->persist(
                    new Supply($supplier, $part, $supplyItem->price, $supplyItem->quantity)
                );
            }

            $this->em->persist(new PartnerSupplyImport($supply->id, $supply->date));

            $this->em->flush();
            $this->em->commit();
        }
    }
}
