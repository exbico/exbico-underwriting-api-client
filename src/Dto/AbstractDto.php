<?php
declare(strict_types=1);

namespace Exbico\Underwriting\Dto;

use ReflectionClass;

abstract class AbstractDto
{
    public function __construct(array $attributes = [])
    {
        $class = new ReflectionClass(static::class);
        foreach ($class->getProperties() as $reflectionProperty) {
            $property = $reflectionProperty->getName();
            if (isset($attributes[$property])) {
                $this->{$property} = $attributes[$property];
            }
        }
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}