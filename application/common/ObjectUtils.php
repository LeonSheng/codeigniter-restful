<?php
/** @noinspection PhpDeprecationInspection */
defined('BASEPATH') OR exit('No direct script access allowed');

use Doctrine\ORM\Proxy\Proxy;

class ObjectUtils
{
    /**
     * Serialize object(s) to array
     *
     * @param object | array $object
     * @return array
     * @throws ReflectionException
     */
    public static function toArray($object): array
    {
        if (is_array($object)) {
            $data_array = array();
            if (count($object) > 0) {
                $class = new ReflectionClass($object[0]);
                foreach ($object as $item) {
                    array_push($data_array, self::toAssociateArray($item, $class));
                }
            }
            return $data_array;
        }
        $class = new ReflectionClass($object);
        return self::toAssociateArray($object, $class);
    }

    /**
     * Serialize object to associative array(s)
     *
     * @param object $object
     * @param ReflectionClass $class
     * @return array
     * @throws ReflectionException
     */
    private static function toAssociateArray(object $object, ReflectionClass& $class): array
    {
        $associateArray = array();
        if ($object instanceof Proxy) {
            $object->__load();
            $class = $class->getParentClass();
        }
        self::assignValueToArray($associateArray, $object, $class);
        $parentClass = $class->getParentClass();
        while ($parentClass) {
            self::assignValueToArray($associateArray, $object, $parentClass);
            $parentClass = $parentClass->getParentClass();
        }
        return $associateArray;
    }

    /**
     * @param array $array
     * @param object $object
     * @param ReflectionClass $class
     * @throws ReflectionException
     */
    private static function assignValueToArray(array &$array, object &$object, ReflectionClass &$class)
    {
        foreach ($class->getProperties() as $property) {
            $property->setAccessible(true);
            $name = $property->getName();
            $value = $property->getValue($object);
            $docComment = $property->getDocComment();
            if ($docComment !== FALSE && strpos($docComment, 'JsonIgnore') !== FALSE) {
                continue;
            }
            if (is_object($value)) {
                if ($value instanceof DateTime) {
                    $value = $value->getTimestamp() * 1000;
                    $array[$name] = $value;
                } elseif ($value instanceof Proxy) {
                    $array[$name] = self::toArray($value);
                }
            } else {
                $array[$name] = $value;
            }
        }
    }

    /**
     * Deserialize associative array to object
     * @param array $array
     * @param string $className
     * @return object
     * @throws ReflectionException
     */
    public static function fromArray(array $array, string $className): object
    {
        $class = new ReflectionClass($className);
        $object = $class->newInstance();
        self::assignValueToObject($array, $object, $class);
        $parentClass = $class->getParentClass();
        while ($parentClass) {
            self::assignValueToObject($array, $object, $parentClass);
            $parentClass = $parentClass->getParentClass();
        }
        return $object;
    }

    /**
     * @param array $array
     * @param object $object
     * @param ReflectionClass $class
     */
    private static function assignValueToObject(array $array, object $object, ReflectionClass $class)
    {
        if (!$class) {
            return;
        }
        foreach ($class->getProperties() as $property) {
            $name = $property->getName();
            if (array_key_exists($name, $array)) {
                $value = $array[$name];
                if (!is_array($value)) {
                    $property->setAccessible(true);
                    $property->setValue($object, $value);
                }
            }
        }
    }
}
