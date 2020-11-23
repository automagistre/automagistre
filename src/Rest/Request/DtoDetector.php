<?php

declare(strict_types=1);

namespace App\Rest\Request;

use function class_exists;
use function get_class;
use function is_object;
use function is_string;
use function str_ends_with;
use function str_starts_with;

final class DtoDetector
{
    /**
     * @param mixed $type
     */
    public function isDto($type): bool
    {
        if (is_object($type)) {
            $type = get_class($type);
        }

        return is_string($type)
            && class_exists($type)
            && str_starts_with($type, 'App\\')
            && str_ends_with($type, 'Dto');
    }
}
