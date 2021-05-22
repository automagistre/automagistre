<?php

declare(strict_types=1);

namespace App\Order\Form;

use App\Form\Model\Model;
use App\Order\Entity\Order;
use App\Order\Entity\OrderItem;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress MissingConstructor
 */
abstract class OrderItemModel extends Model
{
    /**
     * @var Order
     */
    #[Assert\NotBlank]
    public $order;

    public ?OrderItem $parent = null;
}
