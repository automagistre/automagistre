<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Diagrecord.
 *
 * @ORM\Table(name="diagrecord")
 * @ORM\Entity
 */
class Diagrecord
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
     * @ORM\Column(name="item_id", type="integer", nullable=true)
     */
    private $itemId;

    /**
     * @var int
     *
     * @ORM\Column(name="_order_id", type="integer", nullable=true)
     */
    private $orderId;

    /**
     * @var bool
     *
     * @ORM\Column(name="ammo_front", type="boolean", nullable=true)
     */
    private $ammoFront;

    /**
     * @var bool
     *
     * @ORM\Column(name="ammo_rear", type="boolean", nullable=true)
     */
    private $ammoRear;

    /**
     * @var bool
     *
     * @ORM\Column(name="sup_bear", type="boolean", nullable=true)
     */
    private $supBear;

    /**
     * @var bool
     *
     * @ORM\Column(name="near_light", type="boolean", nullable=true)
     */
    private $nearLight;

    /**
     * @var bool
     *
     * @ORM\Column(name="far_light", type="boolean", nullable=true)
     */
    private $farLight;

    /**
     * @var bool
     *
     * @ORM\Column(name="parking_light", type="boolean", nullable=true)
     */
    private $parkingLight;

    /**
     * @var bool
     *
     * @ORM\Column(name="blinker", type="boolean", nullable=true)
     */
    private $blinker;

    /**
     * @var bool
     *
     * @ORM\Column(name="plate_light", type="boolean", nullable=true)
     */
    private $plateLight;

    /**
     * @var bool
     *
     * @ORM\Column(name="stop_light", type="boolean", nullable=true)
     */
    private $stopLight;

    /**
     * @var bool
     *
     * @ORM\Column(name="horn", type="boolean", nullable=true)
     */
    private $horn;

    /**
     * @var bool
     *
     * @ORM\Column(name="dash_light", type="boolean", nullable=true)
     */
    private $dashLight;

    /**
     * @var bool
     *
     * @ORM\Column(name="warning_light", type="boolean", nullable=true)
     */
    private $warningLight;

    /**
     * @var bool
     *
     * @ORM\Column(name="handbrake", type="boolean", nullable=true)
     */
    private $handbrake;

    /**
     * @var bool
     *
     * @ORM\Column(name="air_filter", type="boolean", nullable=true)
     */
    private $airFilter;

    /**
     * @var bool
     *
     * @ORM\Column(name="driving_belt", type="boolean", nullable=true)
     */
    private $drivingBelt;

    /**
     * @var bool
     *
     * @ORM\Column(name="hv_wires", type="boolean", nullable=true)
     */
    private $hvWires;

    /**
     * @var bool
     *
     * @ORM\Column(name="eng_noise", type="boolean", nullable=true)
     */
    private $engNoise;

    /**
     * @var bool
     *
     * @ORM\Column(name="oil_level", type="boolean", nullable=true)
     */
    private $oilLevel;

    /**
     * @var bool
     *
     * @ORM\Column(name="engine_mount", type="boolean", nullable=true)
     */
    private $engineMount;

    /**
     * @var bool
     *
     * @ORM\Column(name="antifreeze_level", type="boolean", nullable=true)
     */
    private $antifreezeLevel;

    /**
     * @var bool
     *
     * @ORM\Column(name="at_level", type="boolean", nullable=true)
     */
    private $atLevel;

    /**
     * @var bool
     *
     * @ORM\Column(name="clutch_level", type="boolean", nullable=true)
     */
    private $clutchLevel;

    /**
     * @var bool
     *
     * @ORM\Column(name="brake_level", type="boolean", nullable=true)
     */
    private $brakeLevel;

    /**
     * @var bool
     *
     * @ORM\Column(name="hydro_level", type="boolean", nullable=true)
     */
    private $hydroLevel;

    /**
     * @var bool
     *
     * @ORM\Column(name="shock_dust_front", type="boolean", nullable=true)
     */
    private $shockDustFront;

    /**
     * @var bool
     *
     * @ORM\Column(name="shock_dust_rear", type="boolean", nullable=true)
     */
    private $shockDustRear;

    /**
     * @var bool
     *
     * @ORM\Column(name="brake_disk_front", type="boolean", nullable=true)
     */
    private $brakeDiskFront;

    /**
     * @var bool
     *
     * @ORM\Column(name="brake_pad_front", type="boolean", nullable=true)
     */
    private $brakePadFront;

    /**
     * @var bool
     *
     * @ORM\Column(name="support_front", type="boolean", nullable=true)
     */
    private $supportFront;

    /**
     * @var bool
     *
     * @ORM\Column(name="bearing_front", type="boolean", nullable=true)
     */
    private $bearingFront;

    /**
     * @var bool
     *
     * @ORM\Column(name="brake_disk_rear", type="boolean", nullable=true)
     */
    private $brakeDiskRear;

    /**
     * @var bool
     *
     * @ORM\Column(name="brake_pad_rear", type="boolean", nullable=true)
     */
    private $brakePadRear;

    /**
     * @var bool
     *
     * @ORM\Column(name="support_rear", type="boolean", nullable=true)
     */
    private $supportRear;

    /**
     * @var bool
     *
     * @ORM\Column(name="bearing_rear", type="boolean", nullable=true)
     */
    private $bearingRear;

    /**
     * @var bool
     *
     * @ORM\Column(name="arm_lower_front", type="boolean", nullable=true)
     */
    private $armLowerFront;

    /**
     * @var bool
     *
     * @ORM\Column(name="arm_upper_front", type="boolean", nullable=true)
     */
    private $armUpperFront;

    /**
     * @var bool
     *
     * @ORM\Column(name="arm_lower_rear", type="boolean", nullable=true)
     */
    private $armLowerRear;

    /**
     * @var bool
     *
     * @ORM\Column(name="arm_upper_rear", type="boolean", nullable=true)
     */
    private $armUpperRear;

    /**
     * @var bool
     *
     * @ORM\Column(name="steer_tip", type="boolean", nullable=true)
     */
    private $steerTip;

    /**
     * @var bool
     *
     * @ORM\Column(name="steer_rod", type="boolean", nullable=true)
     */
    private $steerRod;

    /**
     * @var bool
     *
     * @ORM\Column(name="steer_rack", type="boolean", nullable=true)
     */
    private $steerRack;

    /**
     * @var bool
     *
     * @ORM\Column(name="engine_leak", type="boolean", nullable=true)
     */
    private $engineLeak;

    /**
     * @var bool
     *
     * @ORM\Column(name="antifreeze_leak", type="boolean", nullable=true)
     */
    private $antifreezeLeak;

    /**
     * @var bool
     *
     * @ORM\Column(name="engine_mount_lower", type="boolean", nullable=true)
     */
    private $engineMountLower;

    /**
     * @var bool
     *
     * @ORM\Column(name="transmission_leak", type="boolean", nullable=true)
     */
    private $transmissionLeak;

    /**
     * @var bool
     *
     * @ORM\Column(name="transmission_rubber", type="boolean", nullable=true)
     */
    private $transmissionRubber;

    /**
     * @var bool
     *
     * @ORM\Column(name="driveshaft", type="boolean", nullable=true)
     */
    private $driveshaft;

    /**
     * @var bool
     *
     * @ORM\Column(name="rear_reductor_leak", type="boolean", nullable=true)
     */
    private $rearReductorLeak;

    /**
     * @var bool
     *
     * @ORM\Column(name="front_reductor_leak", type="boolean", nullable=true)
     */
    private $frontReductorLeak;

    /**
     * @var bool
     *
     * @ORM\Column(name="transfer_leak", type="boolean", nullable=true)
     */
    private $transferLeak;

    /**
     * @var bool
     *
     * @ORM\Column(name="front_stab", type="boolean", nullable=true)
     */
    private $frontStab;

    /**
     * @var bool
     *
     * @ORM\Column(name="front_arm_lower", type="boolean", nullable=true)
     */
    private $frontArmLower;

    /**
     * @var bool
     *
     * @ORM\Column(name="front_arm_upper", type="boolean", nullable=true)
     */
    private $frontArmUpper;

    /**
     * @var bool
     *
     * @ORM\Column(name="rear_arm_lower", type="boolean", nullable=true)
     */
    private $rearArmLower;

    /**
     * @var bool
     *
     * @ORM\Column(name="rear_arm_upper", type="boolean", nullable=true)
     */
    private $rearArmUpper;

    /**
     * @var bool
     *
     * @ORM\Column(name="rear_stab", type="boolean", nullable=true)
     */
    private $rearStab;

    /**
     * @var bool
     *
     * @ORM\Column(name="track_bar", type="boolean", nullable=true)
     */
    private $trackBar;

    /**
     * @var bool
     *
     * @ORM\Column(name="breaking_hose_front", type="boolean", nullable=true)
     */
    private $breakingHoseFront;

    /**
     * @var bool
     *
     * @ORM\Column(name="breaking_hose_rear", type="boolean", nullable=true)
     */
    private $breakingHoseRear;

    /**
     * @var bool
     *
     * @ORM\Column(name="steering_dust_boot", type="boolean", nullable=true)
     */
    private $steeringDustBoot;

    /**
     * @var bool
     *
     * @ORM\Column(name="hydro_leak", type="boolean", nullable=true)
     */
    private $hydroLeak;

    /**
     * @var int
     *
     * @ORM\Column(name="spark", type="integer", nullable=true)
     */
    private $spark;

    /**
     * @var int
     *
     * @ORM\Column(name="engine_belt", type="integer", nullable=true)
     */
    private $engineBelt;

    /**
     * @var int
     *
     * @ORM\Column(name="engine_oil", type="integer", nullable=true)
     */
    private $engineOil;

    /**
     * @var int
     *
     * @ORM\Column(name="gear_oil", type="integer", nullable=true)
     */
    private $gearOil;

    /**
     * @var int
     *
     * @ORM\Column(name="reductor_rear_oil", type="integer", nullable=true)
     */
    private $reductorRearOil;

    /**
     * @var int
     *
     * @ORM\Column(name="reductor_front_oil", type="integer", nullable=true)
     */
    private $reductorFrontOil;

    /**
     * @var int
     *
     * @ORM\Column(name="transfer_oil", type="integer", nullable=true)
     */
    private $transferOil;

    /**
     * @var int
     *
     * @ORM\Column(name="hydro_oil", type="integer", nullable=true)
     */
    private $hydroOil;

    /**
     * @var int
     *
     * @ORM\Column(name="filter_cabin", type="integer", nullable=true)
     */
    private $filterCabin;
}
