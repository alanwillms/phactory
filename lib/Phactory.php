<?php

use \Phactory\HasOneRelationship;
use \Phactory\Dependency;
use \Phactory\Builder;
use \Phactory\Loader;
use \Phactory\Fixtures;
use \Phactory\Triggers;

class Phactory
{
    private static $loader;
    private static $builder;
    private static $fixtures;
    private static $triggers;

    public static function reset()
    {
        self::$loader = null;
        self::$builder = null;
        self::$fixtures = null;
        self::$triggers = null;
    }

    public static function hasOne($name, $arguments = array())
    {
        $arguments = func_get_args();
        array_shift($arguments);

        list($type, $override) = self::resolveArgs($arguments);

        return new HasOneRelationship($name, $type, $override);
    }

    public static function uses($dependancy)
    {
        return new Dependency($dependancy);
    }

    public static function __callStatic($name, $arguments = array())
    {
        list($type, $override) = self::resolveArgs($arguments);

        return self::createBlueprint($name, $type, $override);
    }

    public static function createBlueprint($name, $type, $override = array())
    {
        if (self::fixtures()->hasFixture($name, $type)) {
            return self::fixtures()->getFixture($name, $type);
        }

        $blueprint = self::getBlueprint($name, $type, $override);

        $object = self::builder()->create($blueprint);

        if ($blueprint->isFixture()) {
            self::fixtures()->setFixture($name, $type, $object);
        }

        return $object;
    }

    public static function getBlueprint($name, $type, $override = array())
    {
        $factory = self::loader()->load($name);

        return $factory->create($type, $override);
    }

    public static function loader($loader = null)
    {
        if (is_object($loader)) {
            self::$loader = $loader;
        }

        return isset(self::$loader) ? self::$loader : self::$loader = new Loader;
    }

    public static function builder($builder = null)
    {
        if (is_object($builder)) {
            self::$builder = $builder;
        }

        return isset(self::$builder) ? self::$builder : self::$builder = new Builder;
    }

    public static function fixtures()
    {
        return isset(self::$fixtures) ? self::$fixtures : self::$fixtures = new Fixtures;
    }

    public static function triggers($triggers = null)
    {
        if (is_object($triggers)) {
            self::$triggers = new Triggers($triggers);
        }

        return isset(self::$triggers) ? self::$triggers : self::$triggers = new Triggers;
    }

    private static function resolveArgs($args)
    {
        $type = 'blueprint';
        $override = array();

        if (count($args) == 2) {
            $type = $args[0] ? : 'blueprint';
            $override = $args[1] ? : array();
        } elseif (count($args) == 1) {
            if (is_string($args[0])) {
                $type = $args[0];
            } elseif (is_array($args[0])) {
                $override = $args[0];
            }
                
        }

        return array($type, $override);
    }
}
