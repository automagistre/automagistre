<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Diagrecord
 *
 * @ORM\Table(name="diagrecord")
 * @ORM\Entity
 */
class Diagrecord
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="item_id", type="integer", nullable=true)
     */
    private $itemId;

    /**
     * @var integer
     *
     * @ORM\Column(name="_order_id", type="integer", nullable=true)
     */
    private $orderId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="ammo_front", type="boolean", nullable=true)
     */
    private $ammoFront;

    /**
     * @var boolean
     *
     * @ORM\Column(name="ammo_rear", type="boolean", nullable=true)
     */
    private $ammoRear;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sup_bear", type="boolean", nullable=true)
     */
    private $supBear;

    /**
     * @var boolean
     *
     * @ORM\Column(name="near_light", type="boolean", nullable=true)
     */
    private $nearLight;

    /**
     * @var boolean
     *
     * @ORM\Column(name="far_light", type="boolean", nullable=true)
     */
    private $farLight;

    /**
     * @var boolean
     *
     * @ORM\Column(name="parking_light", type="boolean", nullable=true)
     */
    private $parkingLight;

    /**
     * @var boolean
     *
     * @ORM\Column(name="blinker", type="boolean", nullable=true)
     */
    private $blinker;

    /**
     * @var boolean
     *
     * @ORM\Column(name="plate_light", type="boolean", nullable=true)
     */
    private $plateLight;

    /**
     * @var boolean
     *
     * @ORM\Column(name="stop_light", type="boolean", nullable=true)
     */
    private $stopLight;

    /**
     * @var boolean
     *
     * @ORM\Column(name="horn", type="boolean", nullable=true)
     */
    private $horn;

    /**
     * @var boolean
     *
     * @ORM\Column(name="dash_light", type="boolean", nullable=true)
     */
    private $dashLight;

    /**
     * @var boolean
     *
     * @ORM\Column(name="warning_light", type="boolean", nullable=true)
     */
    private $warningLight;

    /**
     * @var boolean
     *
     * @ORM\Column(name="handbrake", type="boolean", nullable=true)
     */
    private $handbrake;

    /**
     * @var boolean
     *
     * @ORM\Column(name="air_filter", type="boolean", nullable=true)
     */
    private $airFilter;

    /**
     * @var boolean
     *
     * @ORM\Column(name="driving_belt", type="boolean", nullable=true)
     */
    private $drivingBelt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="hv_wires", type="boolean", nullable=true)
     */
    private $hvWires;

    /**
     * @var boolean
     *
     * @ORM\Column(name="eng_noise", type="boolean", nullable=true)
     */
    private $engNoise;

    /**
     * @var boolean
     *
     * @ORM\Column(name="oil_level", type="boolean", nullable=true)
     */
    private $oilLevel;

    /**
     * @var boolean
     *
     * @ORM\Column(name="engine_mount", type="boolean", nullable=true)
     */
    private $engineMount;

    /**
     * @var boolean
     *
     * @ORM\Column(name="antifreeze_level", type="boolean", nullable=true)
     */
    private $antifreezeLevel;

    /**
     * @var boolean
     *
     * @ORM\Column(name="at_level", type="boolean", nullable=true)
     */
    private $atLevel;

    /**
     * @var boolean
     *
     * @ORM\Column(name="clutch_level", type="boolean", nullable=true)
     */
    private $clutchLevel;

    /**
     * @var boolean
     *
     * @ORM\Column(name="brake_level", type="boolean", nullable=true)
     */
    private $brakeLevel;

    /**
     * @var boolean
     *
     * @ORM\Column(name="hydro_level", type="boolean", nullable=true)
     */
    private $hydroLevel;

    /**
     * @var boolean
     *
     * @ORM\Column(name="shock_dust_front", type="boolean", nullable=true)
     */
    private $shockDustFront;

    /**
     * @var boolean
     *
     * @ORM\Column(name="shock_dust_rear", type="boolean", nullable=true)
     */
    private $shockDustRear;

    /**
     * @var boolean
     *
     * @ORM\Column(name="brake_disk_front", type="boolean", nullable=true)
     */
    private $brakeDiskFront;

    /**
     * @var boolean
     *
     * @ORM\Column(name="brake_pad_front", type="boolean", nullable=true)
     */
    private $brakePadFront;

    /**
     * @var boolean
     *
     * @ORM\Column(name="support_front", type="boolean", nullable=true)
     */
    private $supportFront;

    /**
     * @var boolean
     *
     * @ORM\Column(name="bearing_front", type="boolean", nullable=true)
     */
    private $bearingFront;

    /**
     * @var boolean
     *
     * @ORM\Column(name="brake_disk_rear", type="boolean", nullable=true)
     */
    private $brakeDiskRear;

    /**
     * @var boolean
     *
     * @ORM\Column(name="brake_pad_rear", type="boolean", nullable=true)
     */
    private $brakePadRear;

    /**
     * @var boolean
     *
     * @ORM\Column(name="support_rear", type="boolean", nullable=true)
     */
    private $supportRear;

    /**
     * @var boolean
     *
     * @ORM\Column(name="bearing_rear", type="boolean", nullable=true)
     */
    private $bearingRear;

    /**
     * @var boolean
     *
     * @ORM\Column(name="arm_lower_front", type="boolean", nullable=true)
     */
    private $armLowerFront;

    /**
     * @var boolean
     *
     * @ORM\Column(name="arm_upper_front", type="boolean", nullable=true)
     */
    private $armUpperFront;

    /**
     * @var boolean
     *
     * @ORM\Column(name="arm_lower_rear", type="boolean", nullable=true)
     */
    private $armLowerRear;

    /**
     * @var boolean
     *
     * @ORM\Column(name="arm_upper_rear", type="boolean", nullable=true)
     */
    private $armUpperRear;

    /**
     * @var boolean
     *
     * @ORM\Column(name="steer_tip", type="boolean", nullable=true)
     */
    private $steerTip;

    /**
     * @var boolean
     *
     * @ORM\Column(name="steer_rod", type="boolean", nullable=true)
     */
    private $steerRod;

    /**
     * @var boolean
     *
     * @ORM\Column(name="steer_rack", type="boolean", nullable=true)
     */
    private $steerRack;

    /**
     * @var boolean
     *
     * @ORM\Column(name="engine_leak", type="boolean", nullable=true)
     */
    private $engineLeak;

    /**
     * @var boolean
     *
     * @ORM\Column(name="antifreeze_leak", type="boolean", nullable=true)
     */
    private $antifreezeLeak;

    /**
     * @var boolean
     *
     * @ORM\Column(name="engine_mount_lower", type="boolean", nullable=true)
     */
    private $engineMountLower;

    /**
     * @var boolean
     *
     * @ORM\Column(name="transmission_leak", type="boolean", nullable=true)
     */
    private $transmissionLeak;

    /**
     * @var boolean
     *
     * @ORM\Column(name="transmission_rubber", type="boolean", nullable=true)
     */
    private $transmissionRubber;

    /**
     * @var boolean
     *
     * @ORM\Column(name="driveshaft", type="boolean", nullable=true)
     */
    private $driveshaft;

    /**
     * @var boolean
     *
     * @ORM\Column(name="rear_reductor_leak", type="boolean", nullable=true)
     */
    private $rearReductorLeak;

    /**
     * @var boolean
     *
     * @ORM\Column(name="front_reductor_leak", type="boolean", nullable=true)
     */
    private $frontReductorLeak;

    /**
     * @var boolean
     *
     * @ORM\Column(name="transfer_leak", type="boolean", nullable=true)
     */
    private $transferLeak;

    /**
     * @var boolean
     *
     * @ORM\Column(name="front_stab", type="boolean", nullable=true)
     */
    private $frontStab;

    /**
     * @var boolean
     *
     * @ORM\Column(name="front_arm_lower", type="boolean", nullable=true)
     */
    private $frontArmLower;

    /**
     * @var boolean
     *
     * @ORM\Column(name="front_arm_upper", type="boolean", nullable=true)
     */
    private $frontArmUpper;

    /**
     * @var boolean
     *
     * @ORM\Column(name="rear_arm_lower", type="boolean", nullable=true)
     */
    private $rearArmLower;

    /**
     * @var boolean
     *
     * @ORM\Column(name="rear_arm_upper", type="boolean", nullable=true)
     */
    private $rearArmUpper;

    /**
     * @var boolean
     *
     * @ORM\Column(name="rear_stab", type="boolean", nullable=true)
     */
    private $rearStab;

    /**
     * @var boolean
     *
     * @ORM\Column(name="track_bar", type="boolean", nullable=true)
     */
    private $trackBar;

    /**
     * @var boolean
     *
     * @ORM\Column(name="breaking_hose_front", type="boolean", nullable=true)
     */
    private $breakingHoseFront;

    /**
     * @var boolean
     *
     * @ORM\Column(name="breaking_hose_rear", type="boolean", nullable=true)
     */
    private $breakingHoseRear;

    /**
     * @var boolean
     *
     * @ORM\Column(name="steering_dust_boot", type="boolean", nullable=true)
     */
    private $steeringDustBoot;

    /**
     * @var boolean
     *
     * @ORM\Column(name="hydro_leak", type="boolean", nullable=true)
     */
    private $hydroLeak;

    /**
     * @var integer
     *
     * @ORM\Column(name="spark", type="integer", nullable=true)
     */
    private $spark;

    /**
     * @var integer
     *
     * @ORM\Column(name="engine_belt", type="integer", nullable=true)
     */
    private $engineBelt;

    /**
     * @var integer
     *
     * @ORM\Column(name="engine_oil", type="integer", nullable=true)
     */
    private $engineOil;

    /**
     * @var integer
     *
     * @ORM\Column(name="gear_oil", type="integer", nullable=true)
     */
    private $gearOil;

    /**
     * @var integer
     *
     * @ORM\Column(name="reductor_rear_oil", type="integer", nullable=true)
     */
    private $reductorRearOil;

    /**
     * @var integer
     *
     * @ORM\Column(name="reductor_front_oil", type="integer", nullable=true)
     */
    private $reductorFrontOil;

    /**
     * @var integer
     *
     * @ORM\Column(name="transfer_oil", type="integer", nullable=true)
     */
    private $transferOil;

    /**
     * @var integer
     *
     * @ORM\Column(name="hydro_oil", type="integer", nullable=true)
     */
    private $hydroOil;

    /**
     * @var integer
     *
     * @ORM\Column(name="filter_cabin", type="integer", nullable=true)
     */
    private $filterCabin;

}

