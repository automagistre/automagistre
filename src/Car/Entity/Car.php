<?php

declare(strict_types=1);

namespace App\Car\Entity;

use App\Car\Enum\BodyType;
use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Entity\Landlord\Operand;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\Mapping as ORM;
use function in_array;
use LogicException;
use function mb_convert_case;
use Money\Currency;
use Money\Money;
use function sprintf;
use function str_replace;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="car")
 *
 * @UniqueEntity(fields={"identifier"})
 */
class Car
{
    use Identity;
    use CreatedAt;

    /**
     * @ORM\Column(type="car_id", unique=true)
     */
    public CarId $uuid;

    /**
     * @Assert\Valid
     *
     * @ORM\Embedded(class="App\Car\Entity\Equipment")
     */
    public ?Equipment $equipment = null;

    /**
     * @Assert\NotBlank
     *
     * @ORM\ManyToOne(targetEntity="App\Car\Entity\Model")
     * @ORM\JoinColumn
     */
    public ?Model $model = null;

    /**
     * @Assert\Length(max="17")
     *
     * @ORM\Column(length=17, nullable=true, unique=true)
     */
    public ?string $identifier = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    public ?int $year = null;

    /**
     * @ORM\Column(type="carcase_enum")
     */
    public ?BodyType $caseType = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Landlord\Operand")
     * @ORM\JoinColumn
     */
    public ?Operand $owner = null;

    /**
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    public ?string $description = null;

    /**
     * @var Collection<int, Recommendation>
     *
     * @ORM\OneToMany(targetEntity="App\Car\Entity\Recommendation", mappedBy="car", cascade={"persist"})
     * @ORM\OrderBy({"createdAt": "ASC"})
     */
    private ?Collection $recommendations;

    /**
     * license plate.
     *
     * @ORM\Column(nullable=true)
     */
    private ?string $gosnomer = null;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $mileage = 0;

    public function __construct()
    {
        $this->uuid = CarId::generate();
        $this->equipment = new Equipment();
        $this->caseType = BodyType::unknown();
        $this->recommendations = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(bool $full = false): string
    {
        $string = '';

        if (null !== $this->model) {
            $string = $this->model->getDisplayName(false);
        }

        if (null !== $this->year) {
            $string .= sprintf(' - %sг.', $this->year);
        }

        if ($full) {
            $string .= sprintf(' (%s)', $this->equipment->toString());
        }

        if ('' === $string) {
            return 'Не определено';
        }

        return $string;
    }

    public function getRecommendationPrice(string $type = null): Money
    {
        if (!in_array($type, ['service', 'part', null], true)) {
            throw new LogicException(sprintf('Unexpected type "%s".', $type));
        }

        $price = new Money(0, new Currency('RUB'));
        foreach ($this->getRecommendations() as $item) {
            if ('service' === $type || null === $type) {
                $price = $price->add($item->getPrice());
            }

            if ('part' === $type || null === $type) {
                $price = $price->add($item->getTotalPartPrice());
            }
        }

        return $price;
    }

    public function setMileage(?int $mileage): void
    {
        if (null !== $mileage) {
            $this->mileage = $mileage;
        }
    }

    public function getMileage(): ?int
    {
        return $this->mileage;
    }

    public function getGosnomer(): ?string
    {
        if (null === $this->gosnomer) {
            return null;
        }

        $roman = ['A', 'B', 'E', 'K', 'M', 'H', 'O', 'P', 'C', 'T', 'Y', 'X'];
        $cyrillic = ['А', 'В', 'Е', 'К', 'М', 'Н', 'О', 'Р', 'С', 'Т', 'У', 'Х'];

        return str_replace($roman, $cyrillic, mb_convert_case($this->gosnomer, MB_CASE_UPPER, 'UTF-8'));
    }

    public function setGosnomer(string $gosnomer = null): void
    {
        $this->gosnomer = $gosnomer;
    }

    /**
     * @return Recommendation[]
     */
    public function getRecommendations(Criteria $criteria = null): array
    {
        if (!$this->recommendations instanceof Selectable) {
            throw new LogicException(sprintf('Collection expected to be implement %s', Selectable::class));
        }

        $criteria = $criteria ?? Criteria::create()->andWhere(Criteria::expr()->isNull('expiredAt'));

        return $this->recommendations->matching($criteria)->getValues();
    }

    public function addRecommendation(Recommendation $recommendation): void
    {
        $this->recommendations[] = $recommendation;
    }

    public function getCarModificationDisplayName(): string
    {
        if (null === $this->model) {
            return 'Не определено';
        }

        return $this->model->getDisplayName();
    }
}
