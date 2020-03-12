<?php
/** @noinspection PhpDeprecationInspection */
defined('BASEPATH') OR exit('No direct script access allowed');

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Proxy\Proxy;

class RepositoryUtils
{
    /**
     * Merge entity to persistent entity
     *
     * @param object $source
     * @param object $target
     * @throws
     */
    public static function setUpdate(object &$source, object &$target): void
    {
        $isAnyUpdated = false;
        $sourceClass = new ReflectionClass($source);
        $targetClass = new ReflectionClass($target);
        self::putUpdateValue($source, $sourceClass, $target, $targetClass, $isAnyUpdated);
        $sourceParentClass = $sourceClass->getParentClass();
        $targetParentClass = $targetClass->getParentClass();
        while ($sourceParentClass && $targetParentClass) {
            self::putUpdateValue($source, $sourceParentClass, $target, $targetParentClass, $isAnyUpdated);
            $sourceParentClass = $sourceParentClass->getParentClass();
            $targetParentClass = $targetParentClass->getParentClass();
        }
        if ($isAnyUpdated) {
            $target->setUpdateTime(new DateTime());
        }
    }

    private static function putUpdateValue(object &$source, ReflectionClass &$sourceClass,
                                           object &$target, ReflectionClass &$targetClass,
                                           bool &$isAnyUpdated): void
    {
        if ($sourceClass->getName() !== $targetClass->getName())
            return;
        $sourceProperties = $sourceClass->getProperties();
        $targetProperties = $sourceClass->getProperties();
        for ($i = 0; $i < count($sourceProperties); $i++) {
            $sourceProperty = $sourceProperties[$i];
            $targetProperty = $targetProperties[$i];
            $sourceProperty->setAccessible(true);
            $targetProperty->setAccessible(true);
            $sourceValue = $sourceProperty->getValue($source);
            $targetValue = $targetProperty->getValue($target);
            if ($sourceValue !== null && !($sourceValue instanceof Proxy) && $sourceValue !== $targetValue) {
                $targetProperty->setValue($target, $sourceValue);
                $isAnyUpdated = true;
            }
        }
    }

    /**
     * @param object $modelObject
     * @throws
     */
    public static function initializeForCreate(object &$modelObject)
    {
        $class = new ReflectionClass($modelObject);
        self::putCreateValue($modelObject, $class);
        $parentClass = $class->getParentClass();
        while ($parentClass) {
            self::putCreateValue($modelObject, $parentClass);
            $parentClass = $parentClass->getParentClass();
        }
    }

    /**
     * @param object $modelObject
     * @param ReflectionClass $class
     * @throws
     */
    private static function putCreateValue(object &$modelObject, ReflectionClass &$class)
    {
        foreach ($class->getProperties() as $property) {
            $property->setAccessible(true);
            $name = $property->getName();
            $value = $property->getValue($modelObject);
            $method = $class->getMethod('set' . Inflector::classify($name));
            $typeName = $method->getParameters()[0]->getType()->getName();
            if ($value === null) {
                switch ($typeName) {
                    case 'string':
                        $method->invoke($modelObject, '');
                        break;
                    case 'int':
                        $method->invoke($modelObject, 0);
                        break;
                    case 'bool':
                        $method->invoke($modelObject, false);
                        break;
                    case 'DateTime':
                        $method->invoke($modelObject, new DateTime());
                }
                if ($name === 'id') {
                    $method->invoke($modelObject, ObjectId::generate());
                }
            }
        }
    }
}
