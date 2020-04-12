<?php

declare(strict_types=1);

namespace App\JSONRPC;

use Datto\JsonRpc\Exceptions\Exception;
use Datto\JsonRpc\Responses\ErrorResponse;

final class ArgumentException extends Exception
{
    /**
     * @param mixed $data
     */
    public function __construct($data = null)
    {
        parent::__construct('Invalid params', ErrorResponse::INVALID_ARGUMENTS, $data);
    }
}
