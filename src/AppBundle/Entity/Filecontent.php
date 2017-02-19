<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Filecontent.
 *
 * @ORM\Table(name="filecontent")
 * @ORM\Entity
 */
class Filecontent
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="meta_filemodel_id", type="integer", nullable=true)
     */
    private $metaFilemodelId;

    /**
     * @var string
     *
     * @ORM\Column(name="ext", type="string", length=255, nullable=true)
     */
    private $ext;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=255, nullable=true)
     */
    private $filename;

    /**
     * @var int
     *
     * @ORM\Column(name="filemodel_id", type="integer", nullable=true)
     */
    private $filemodelId;
}
