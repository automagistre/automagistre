<?php

declare(strict_types=1);

namespace App\Order\Form\Feedback;

use App\Order\Enum\OrderSatisfaction;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress MissingConstructor
 */
final class FeedbackDto
{
    /**
     * @var OrderSatisfaction
     *
     * @Assert\NotBlank()
     */
    public $satisfaction;
}
