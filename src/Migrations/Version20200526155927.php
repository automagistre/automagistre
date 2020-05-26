<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Costil;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;
use function sprintf;
use function strpos;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20200526155927 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('CREATE TABLE part_price (
          id UUID NOT NULL, 
          part_id UUID NOT NULL, 
          since TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          price_amount BIGINT DEFAULT NULL, 
          price_currency_code VARCHAR(3) DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN part_price.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN part_price.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN part_price.since IS \'(DC2Type:datetime_immutable)\'');

        $this->addSql('CREATE TABLE part_discount (
          id UUID NOT NULL, 
          part_id UUID NOT NULL, 
          since TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
          discount_amount BIGINT DEFAULT NULL, 
          discount_currency_code VARCHAR(3) DEFAULT NULL, 
          PRIMARY KEY(id)
        )');
        $this->addSql('COMMENT ON COLUMN part_discount.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN part_discount.part_id IS \'(DC2Type:part_id)\'');
        $this->addSql('COMMENT ON COLUMN part_discount.since IS \'(DC2Type:datetime_immutable)\'');

        // Data migration
        /** @var Connection $landlord */
        $landlord = $this->container->get('doctrine.dbal.landlord_connection');
        $parts = $landlord->fetchAll('SELECT id, price_amount, discount_amount FROM part WHERE price_amount > 0 OR discount_amount > 0');
        foreach ($parts as ['id' => $partId, 'price_amount' => $price, 'discount_amount' => $discount]) {
            if ($price > 0) {
                $this->addSql(
                    sprintf(
                        'INSERT INTO part_price (id, part_id, since, price_amount, price_currency_code) 
                            VALUES (\'%s\'::uuid, \'%s\'::uuid, now(), %s, \'RUB\')',
                        Uuid::uuid6()->toString(),
                        $partId,
                        $price
                    ),
                );
            }

            if ($discount > 0) {
                $this->addSql(
                    sprintf(
                        'INSERT INTO part_discount (id, part_id, since, discount_amount, discount_currency_code) 
                            VALUES (\'%s\'::uuid, \'%s\'::uuid, now(), %s, \'RUB\')',
                        Uuid::uuid6()->toString(),
                        $partId,
                        $discount
                    ),
                );
            }
        }
        $this->addSql(sprintf(
            'INSERT INTO created_by (id, user_id, created_at) SELECT id, \'%s\'::uuid, since FROM part_price',
            Costil::OLD_USER
        ));
        $this->addSql(sprintf(
            'INSERT INTO created_by (id, user_id, created_at) SELECT id, \'%s\'::uuid, since FROM part_discount',
            Costil::OLD_USER
        ));
        // Data migration
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('DROP TABLE part_price');
        $this->addSql('DROP TABLE part_discount');
    }
}
