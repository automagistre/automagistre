<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Messagetranslation.
 *
 * @ORM\Table(name="messagetranslation", uniqueConstraints={@ORM\UniqueConstraint(name="source_language_translation_Index", columns={"messagesource_id", "language", "translation"})})
 * @ORM\Entity
 */
class Messagetranslation
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
     * @ORM\Column(name="messagesource_id", type="integer", nullable=true)
     */
    private $messagesourceId;

    /**
     * @var string
     *
     * @ORM\Column(name="translation", type="blob", length=65535, nullable=true)
     */
    private $translation;

    /**
     * @var string
     *
     * @ORM\Column(name="language", type="string", length=16, nullable=true)
     */
    private $language;
}
