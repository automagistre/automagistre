<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
abstract class Model
{
    public function __construct(array $fields = [])
    {
        foreach ($fields as $field => $value) {
            if (\property_exists($this, $field)) {
                $this->{$field} = $value;
            }
        }
    }
}
