<?php

declare(strict_types=1);

namespace App\Car\Form\DTO;

use App\Car\Entity\Recommendation;
use App\Part\Form\PartOfferDto;
use Symfony\Component\Validator\Constraints as Assert;

final class RecommendationPartDto
{
    /**
     * @var Recommendation
     *
     * @Assert\NotBlank
     */
    public $recommendation;

    /**
     * @var PartOfferDto
     *
     * @Assert\Valid
     * @Assert\NotBlank
     */
    public $partOffer;

    public function __construct(Recommendation $recommendation, PartOfferDto $partOffer)
    {
        $this->recommendation = $recommendation;
        $this->partOffer = $partOffer;
    }
}
