<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210201104533 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE customer_transaction SET description = \'Недосдача по незамерзайке\' WHERE id IN (
                \'1eb64623-0fc8-6314-8b83-02420a000af2\',
                \'1eb64622-bcac-6dce-a92f-02420a000af2\',
                \'1eb64621-b706-6218-a13e-02420a000af2\',
                \'1eb64621-55bf-6a7c-adc5-02420a000af2\'
            )');
    }
}
