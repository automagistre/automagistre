<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IntegrationpartPartcart.
 *
 * @ORM\Table(name="integrationpart_partcart", uniqueConstraints={@ORM\UniqueConstraint(name="UQ_b39f5a56f371ede73ac18dadbf89dc2220a86854", columns={"integrationpart_id", "partcart_id"})}, indexes={@ORM\Index(name="index_for_integrationpart_partcart_partcart_id", columns={"partcart_id"}), @ORM\Index(name="index_for_integrationpart_partcart_integrationpart_id", columns={"integrationpart_id"})})
 * @ORM\Entity
 */
class IntegrationpartPartcart
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
     * @ORM\Column(name="partcart_id", type="integer", nullable=true)
     */
    private $partcartId;

    /**
     * @var int
     *
     * @ORM\Column(name="integrationpart_id", type="integer", nullable=true)
     */
    private $integrationpartId;
}
