<?php

declare(strict_types=1);

namespace App\Tests\Publish;

use App\Publish\Entity\Publish;
use App\Publish\Entity\PublishView;
use App\Shared\Doctrine\Registry;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class PublishViewTest extends KernelTestCase
{
    private const UUID = '1eb3d5fc-03cd-644a-83f3-0242ac1e000a';

    public function test(): void
    {
        static::bootKernel();

        $registry = self::$container->get(Registry::class);
        $tester = new PublishTester($registry->manager(), Uuid::fromString(self::UUID));

        self::assertNull($tester->isPublished());

        $tester->publish();
        self::assertTrue($tester->isPublished());

        $tester->unpublish();
        self::assertFalse($tester->isPublished());
    }
}

class PublishTester
{
    private EntityManagerInterface $em;

    private UuidInterface $id;

    public function __construct(EntityManagerInterface $em, UuidInterface $id)
    {
        $this->em = $em;
        $this->id = $id;
    }

    public function isPublished(): ?bool
    {
        $em = $this->em;
        $view = $em->getRepository(PublishView::class)->find($this->id);
        $em->clear();

        return $view instanceof PublishView ? $view->published : null;
    }

    public function publish(): void
    {
        $em = $this->em;

        $em->persist(Publish::create($this->id, true));
        $em->flush();
    }

    public function unpublish(): void
    {
        $em = $this->em;

        $em->persist(Publish::create($this->id, false));
        $em->flush();
    }
}
