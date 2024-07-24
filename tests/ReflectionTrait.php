<?php 

namespace Tests;

trait ReflectionTrait {

    public function getPrivateProperty(Object $object, string $propertyName) {

        $reflection = new \ReflectionClass($object);

        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        
        return $property->getValue($object);
    }
}