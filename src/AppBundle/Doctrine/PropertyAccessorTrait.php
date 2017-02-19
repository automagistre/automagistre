<?php

namespace AppBundle\Doctrine;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
trait PropertyAccessorTrait
{

    public function __get($property)
    {
        return $this->{$property};
    }

    public function __set($property, $value)
    {
        throw new \DomainException('Setting value not allowed');
    }

    public function __isset($property)
    {
        return property_exists($this, $property);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }
}
