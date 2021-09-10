<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210910195929 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $values = <<<'VALUES'
        ('4f476e3b-dcdb-4b32-8282-3c6833ba550c', '60fcb1e6-1240-4bbc-938a-b107823eb47f'),
        ('57ec2612-46e8-46d7-aa82-34c49730bdf0', '3ea34a85-1cd0-465d-9dae-f552b6199235'),
        ('89786b82-0337-48e4-a659-414581c0c2e6', 'eba055ca-963c-4e0d-8b5d-e52e06cc2254'),
        ('973b18d2-d919-424c-9e42-632fae1fe717', '1eaa7365-c86f-6742-adad-02420a0005d6'),
        ('3288c27c-b22c-460e-baf8-d637ff45af7d', '1ebf0b9d-e019-6eda-ad18-02420a000a93'),
        ('500fb136-3881-4faa-9cfc-416ded4183f6', '1eaa6a4e-05d2-6240-ad67-02420a000587'),
        ('bcb2e170-c2db-413e-8dbd-c3a9b485cb19', '0b21cae0-358e-4d6a-a182-841d06e59647'),
        ('1d092a29-1407-4061-a5f2-ae93b474c158', '82e0e54c-0d11-4043-abd3-8b19eb43b400'),
        ('53e1898d-3f6b-4d21-b895-e6091e8615f2', '13efe794-e400-4270-93df-4c54b4ecc932'),
        ('aa90358e-70aa-4e8c-8409-ffc5f1683377', '4ffc24e2-8e60-42e0-9c8f-7a73888b2da6'),
        ('b5f10063-5172-4acb-857c-9d9fdd2c2e87', '123ff639-a882-417e-a01f-b65864dc9f52'),
        ('835cfd16-c90c-45f2-8c87-546d1906baaf', '637a2ff6-a085-428c-87dc-0046abe11cc9'),
        ('28e30a2a-dfa5-4b6d-91f1-f32d6bbd96e5', '59861141-83b2-416c-b672-8ba8a1cb76b2'),
        ('d07b27d8-98d0-4845-ba1e-bad3863fe49b', '8614503b-ca5f-4b3a-8142-dadb011462a0'),
        ('410b4417-58a0-49ae-b6ac-39032475a1a3', '0ed195e6-b46c-4f95-b581-6d5a2e7ebf46'),
        ('c6a64068-dcb2-4e70-a144-147f7abda499', '1eab64c5-18b0-646c-9ac3-0242c0a8100a'),
        ('0dd7f47b-b841-4f2c-991a-ebef41d475d0', '5fe0541f-e192-4ce7-8d87-6bca1d557477'),
        ('b77c6dbc-883d-45ef-8ce4-f62a85e4610b', '642a07d8-db67-48c5-945d-54d784c3ac28'),
        ('6573cd4f-1e67-40a0-b14e-d435d12f72ca', '62398613-c0e0-4291-8613-d6c0cc2ba4b2'),
        ('76128bb3-a202-4e05-a669-5612a37fb090', '80b9dc68-c1ce-4222-9b7c-c8fa0d2ed59c')
        VALUES;

        $this->addSql(
            <<<SQL
            UPDATE user_permission SET user_id = v.new_id::UUID
            FROM (VALUES {$values}) v(new_id, old_id)
            WHERE user_permission.user_id = v.old_id::UUID
            SQL,
        );
    }
}
