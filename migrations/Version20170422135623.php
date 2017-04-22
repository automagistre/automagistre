<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170422135623 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql('INSERT IGNORE INTO `users` VALUES 
        (UNHEX(\'11E72464E370AAB2915135402C34DAD3\'),834,\'preemiere@ya.ru\',\'preemiere@ya.ru\',\'preemiere@ya.ru\',\'preemiere@ya.ru\',1,NULL,\'$2y$13$fVzYlkFaLFS/hQUBj.xURee98FoR0peE391PT1Jah61EggG3xwLlG\',\'2017-04-21 15:58:25\',NULL,NULL,\'a:1:{i:0;s:13:\"ROLE_EMPLOYEE\";}\'),
        (UNHEX(\'11E7246687757A60A2D0FEF101956A0C\'),1881,\'kirillsidorov@gmail.com\',\'kirillsidorov@gmail.com\',\'kirillsidorov@gmail.com\',\'kirillsidorov@gmail.com\',1,NULL,\'$2y$13$ncONy99LlQAJrvAwMeu.EelW1l5RMIb6vxwP3vbUszP1Mhspq.1.W\',\'2017-04-18 18:41:51\',NULL,NULL,\'a:1:{i:0;s:13:\"ROLE_EMPLOYEE\";}\');

');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
