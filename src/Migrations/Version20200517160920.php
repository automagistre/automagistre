<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use function strpos;

final class Version20200517160920 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE operand_transaction ALTER amount_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE operand_transaction ALTER amount_amount TYPE BIGINT USING amount_amount::bigint');
        $this->addSql('ALTER TABLE monthly_salary ALTER amount_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE monthly_salary ALTER amount_amount TYPE BIGINT USING amount_amount::bigint');
        $this->addSql('ALTER TABLE expense_item ALTER amount_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE expense_item ALTER amount_amount TYPE BIGINT USING amount_amount::bigint');
        $this->addSql('ALTER TABLE wallet_transaction ALTER amount_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE wallet_transaction ALTER amount_amount TYPE BIGINT USING amount_amount::bigint');
        $this->addSql('ALTER TABLE car_recommendation ALTER price_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE car_recommendation ALTER price_amount TYPE BIGINT USING price_amount::bigint');
        $this->addSql('ALTER TABLE car_recommendation_part ALTER price_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE car_recommendation_part ALTER price_amount TYPE BIGINT USING price_amount::bigint');
        $this->addSql('ALTER TABLE income ALTER accrued_amount_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE income ALTER accrued_amount_amount TYPE BIGINT USING accrued_amount_amount::bigint');
        $this->addSql('ALTER TABLE income_part ALTER price_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE income_part ALTER price_amount TYPE BIGINT USING price_amount::bigint');
        $this->addSql('ALTER TABLE orders ALTER closed_balance_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE orders ALTER closed_balance_amount TYPE BIGINT USING closed_balance_amount::bigint');
        $this->addSql('ALTER TABLE order_contractor ALTER money_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE order_contractor ALTER money_amount TYPE BIGINT USING money_amount::bigint');
        $this->addSql('ALTER TABLE order_item_part ALTER price_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE order_item_part ALTER price_amount TYPE BIGINT USING price_amount::bigint');
        $this->addSql('ALTER TABLE order_item_part ALTER discount_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE order_item_part ALTER discount_amount TYPE BIGINT USING discount_amount::bigint');
        $this->addSql('ALTER TABLE order_item_service ALTER price_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE order_item_service ALTER price_amount TYPE BIGINT USING price_amount::bigint');
        $this->addSql('ALTER TABLE order_item_service ALTER discount_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE order_item_service ALTER discount_amount TYPE BIGINT USING discount_amount::bigint');
        $this->addSql('ALTER TABLE order_payment ALTER money_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE order_payment ALTER money_amount TYPE BIGINT USING money_amount::bigint');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->skipIf(0 !== strpos($this->connection->getDatabase(), 'tenant'), 'tenant only');

        $this->addSql('ALTER TABLE monthly_salary ALTER amount_amount TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE monthly_salary ALTER amount_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE income ALTER accrued_amount_amount TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE income ALTER accrued_amount_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE expense_item ALTER amount_amount TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE expense_item ALTER amount_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE car_recommendation_part ALTER price_amount TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE car_recommendation_part ALTER price_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE car_recommendation ALTER price_amount TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE car_recommendation ALTER price_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE income_part ALTER price_amount TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE income_part ALTER price_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE operand_transaction ALTER amount_amount TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE operand_transaction ALTER amount_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE order_item_service ALTER price_amount TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE order_item_service ALTER price_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE order_item_service ALTER discount_amount TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE order_item_service ALTER discount_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE order_payment ALTER money_amount TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE order_payment ALTER money_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE wallet_transaction ALTER amount_amount TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE wallet_transaction ALTER amount_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE order_item_part ALTER price_amount TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE order_item_part ALTER price_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE order_item_part ALTER discount_amount TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE order_item_part ALTER discount_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE order_contractor ALTER money_amount TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE order_contractor ALTER money_amount DROP DEFAULT');
        $this->addSql('ALTER TABLE orders ALTER closed_balance_amount TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE orders ALTER closed_balance_amount DROP DEFAULT');
    }
}
