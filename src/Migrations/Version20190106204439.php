<?php

declare(strict_types=1);

namespace App\Migrations;

use App\Costil;
use App\Entity\Order;
use App\Entity\Organization;
use App\Entity\Wallet;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManagerInterface;
use Money\Currency;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190106204439 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE wallet (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, currency_code VARCHAR(3) DEFAULT NULL, INDEX IDX_7C68921F7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE wallet ADD CONSTRAINT FK_7C68921F7E3C61F9 FOREIGN KEY (owner_id) REFERENCES operand (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE employee ADD wallet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A1712520F3 FOREIGN KEY (wallet_id) REFERENCES wallet (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5D9F75A1712520F3 ON employee (wallet_id)');
        $this->addSql('ALTER TABLE operand ADD wallet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE operand ADD CONSTRAINT FK_83E03CE6712520F3 FOREIGN KEY (wallet_id) REFERENCES wallet (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_83E03CE6712520F3 ON operand (wallet_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7C68921F7E3C61F95E237E06 ON wallet (owner_id, name)');

        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840DE92F8F78');

        $this->addSql('INSERT INTO wallet (owner_id, name, currency_code) SELECT operand.id, \'Основной\', \'RUB\' FROM operand');
        $this->addSql('UPDATE operand JOIN wallet w on operand.id = w.owner_id SET wallet_id = w.id');
        $this->addSql('UPDATE employee JOIN person p on employee.person_id = p.id JOIN operand o ON p.id = o.id SET employee.wallet_id = o.wallet_id');
        $this->addSql('UPDATE payment JOIN operand ON recipient_id = operand.id SET payment.recipient_id = operand.wallet_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A1712520F3');
        $this->addSql('ALTER TABLE operand DROP FOREIGN KEY FK_83E03CE6712520F3');
        $this->addSql('DROP TABLE wallet');
        $this->addSql('DROP INDEX UNIQ_5D9F75A1712520F3 ON employee');
        $this->addSql('ALTER TABLE employee DROP wallet_id');
        $this->addSql('DROP INDEX UNIQ_83E03CE6712520F3 ON operand');
        $this->addSql('ALTER TABLE operand DROP wallet_id');

        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DE92F8F78 FOREIGN KEY (recipient_id) REFERENCES operand (id)');
    }

    public function postUp(Schema $schema): void
    {
        $em = $this->container->get('doctrine')->getManager();
        if (!$em instanceof EntityManagerInterface) {
            throw new \LogicException('EntityManager required');
        }

        $em->transactional(function (EntityManagerInterface $em): void {
            $expr = $em->getExpressionBuilder();

            $automagistre = $em->getRepository(Organization::class)->find(Costil::CASHBOX);

            $em->createQueryBuilder()
                ->update(Wallet::class, 'entity')
                ->set('entity.name', '\'Касса\'')
                ->where('entity = :wallet')
                ->setParameter('wallet', $automagistre->getWallet())
                ->getQuery()
                ->execute();

            /** @var Organization[] $organizations */
            $organizations = $em->createQueryBuilder()
                ->select('entity')
                ->from(Organization::class, 'entity')
                ->where($expr->like('entity.name', '\'===%\''))
                ->andWhere($expr->neq('entity.id', $automagistre->getId()))
                ->getQuery()
                ->getResult();

            // Remove empty order
            $em->createQueryBuilder()
                ->delete(Order::class, 'entity')
                ->where('entity.customer = :id')
                ->setParameter('id', 2306)
                ->getQuery()
                ->execute();

            foreach ($organizations as $organization) {
                $name = \ltrim($organization->getName(), ' =');
                $wallet = new Wallet($automagistre, $name, new Currency('RUB'));
                $em->persist($wallet);
                $em->flush();

                $em->getConnection()
                    ->executeQuery(
                        'UPDATE payment SET recipient_id = :idTo WHERE recipient_id = :idFrom',
                        [
                            'idFrom' => $organization->getWallet()->getId(),
                            'idTo' => $wallet->getId(),
                        ]
                    );

                $em->remove($organization->getWallet());
                $em->remove($organization);
            }
        });
    }
}
