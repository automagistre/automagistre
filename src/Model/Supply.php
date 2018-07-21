<?php

declare(strict_types=1);

namespace App\Model;

use DateTime;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Supply extends Model
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var DateTime
     */
    public $date;

    /**
     * TODO.
     *
     * @var mixed
     */
    public $status;

    /**
     * @var SupplyItem[]
     */
    public $items;

    /**
     * @var DateTime
     */
    public $arrivalOrientAt;

    /**
     * @var DateTime
     */
    public $arrivalWarrantyAt;
}
