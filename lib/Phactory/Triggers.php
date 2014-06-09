<?php

namespace Phactory;

/**
 * Triggers executed on creating and persisting an object
 */
class Triggers
{
    /**
     * Triggers object
     * @var object
     */
    private $triggers;

    /**
     * Constructor
     * @param object|null $triggers object defining all triggers
     */
    public function __construct($triggers = null)
    {
        $this->triggers = $triggers;
    }

    /**
     * Execute triggers before saving
     * @param string $name factory name
     * @param string $type variation or fixture name
     * @param object|array $object
     */
    public function beforeSave($name, $type, $object)
    {
        $this->event($name, $type, $object, 'beforeSave');
    }

    /**
     * Execute triggers after saving
     * @param string $name factory name
     * @param string $type variation or fixture name
     * @param object|array $object
     */
    public function afterSave($name, $type, $object)
    {
        $this->event($name, $type, $object, 'afterSave');
    }

    /**
     * Calls trigger events
     * @param string $name factory name
     * @param string $type variation or fixture name
     * @param object|array $object
     * @param string $event event name
     */
    protected function event($name, $type, $object, $event)
    {
        if (is_null($this->triggers)) {
            return;
        }

        $event = ucfirst($event);

        if (method_exists($this->triggers, "{$name}{$event}")) {
            call_user_func(array($this->triggers, "{$name}{$event}"), $object);
        }

        if ($type) {

            $type = ucfirst($type);

            if (method_exists($this->triggers, "{$name}{$type}{$event}")) {
                call_user_func(array($this->triggers, "{$name}{$type}{$event}"), $object);
            }
        }
    }
}
