<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200626081428 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP VIEW IF EXISTS part_view');

        $this->addSql('DROP SEQUENCE order_payment_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE part_cross_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reservation_id_seq CASCADE');

        $this->addSql('ALTER TABLE order_payment ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE order_payment ALTER id TYPE UUID USING (uuid)');
        $this->addSql('ALTER TABLE order_payment DROP uuid');
        $this->addSql('COMMENT ON COLUMN order_payment.id IS \'(DC2Type:uuid)\'');

        // --

        $this->addSql('ALTER TABLE reservation ADD uuid UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE reservation ALTER id TYPE UUID USING (uuid)');
        $this->addSql('ALTER TABLE reservation DROP uuid');
        $this->addSql('COMMENT ON COLUMN reservation.id IS \'(DC2Type:uuid)\'');

        // --

        $this->addSql('ALTER TABLE part_cross ADD uuid UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE part_cross_part ADD uuid UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE part_cross_part DROP CONSTRAINT fk_b98f499c70b9088c');
        $this->addSql('ALTER TABLE part_cross ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE part_cross ALTER id TYPE UUID USING (uuid)');
        $this->addSql('ALTER TABLE part_cross DROP uuid');
        $this->addSql('ALTER TABLE part_cross_part ALTER part_cross_id DROP DEFAULT');
        $this->addSql('ALTER TABLE part_cross_part ALTER part_cross_id TYPE UUID USING (uuid)');
        $this->addSql('ALTER TABLE part_cross_part DROP uuid');
        $this->addSql('ALTER TABLE part_cross_part ADD CONSTRAINT FK_B98F499C70B9088C FOREIGN KEY (part_cross_id) REFERENCES part_cross (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('COMMENT ON COLUMN part_cross.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN part_cross_part.part_cross_id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE order_payment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE part_cross_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reservation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE order_payment ADD uuid UUID NOT NULL');
        $this->addSql('ALTER TABLE order_payment ALTER id TYPE INT');
        $this->addSql('ALTER TABLE order_payment ALTER id DROP DEFAULT');
        $this->addSql('CREATE SEQUENCE order_payment_id_seq');
        $this->addSql('SELECT setval(\'order_payment_id_seq\', (SELECT MAX(id) FROM order_payment))');
        $this->addSql('ALTER TABLE order_payment ALTER id SET DEFAULT nextval(\'order_payment_id_seq\')');
        $this->addSql('COMMENT ON COLUMN order_payment.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN order_payment.id IS NULL');
        $this->addSql('ALTER TABLE reservation ALTER id TYPE INT');
        $this->addSql('ALTER TABLE reservation ALTER id DROP DEFAULT');
        $this->addSql('CREATE SEQUENCE reservation_id_seq');
        $this->addSql('SELECT setval(\'reservation_id_seq\', (SELECT MAX(id) FROM reservation))');
        $this->addSql('ALTER TABLE reservation ALTER id SET DEFAULT nextval(\'reservation_id_seq\')');
        $this->addSql('COMMENT ON COLUMN reservation.id IS NULL');
        $this->addSql('ALTER TABLE part_cross_part ALTER part_cross_id TYPE INT');
        $this->addSql('ALTER TABLE part_cross_part ALTER part_cross_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN part_cross_part.part_cross_id IS NULL');
        $this->addSql('ALTER TABLE part_cross ALTER id TYPE INT');
        $this->addSql('ALTER TABLE part_cross ALTER id DROP DEFAULT');
        $this->addSql('CREATE SEQUENCE part_cross_id_seq');
        $this->addSql('SELECT setval(\'part_cross_id_seq\', (SELECT MAX(id) FROM part_cross))');
        $this->addSql('ALTER TABLE part_cross ALTER id SET DEFAULT nextval(\'part_cross_id_seq\')');
        $this->addSql('COMMENT ON COLUMN part_cross.id IS NULL');
    }
}
