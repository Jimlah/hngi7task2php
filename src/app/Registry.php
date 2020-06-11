<?php declare(strict_types=1);

/*
 * This file/script imitates a service container. It only binds
 * object or resources to the registry. It does not resolve
 * any dependencies.
 *
 */

namespace TeamFlash;

class Registry
{
    /**
     * All registered keys.
     *
     * @var array
     */
    protected static $bindings = [];

    /**
     * Bind a new key/value into the container.
     *
     * @param  string $key
     * @param  mixed  $value
     */
    public static function bind($key, $value)
    {
        static::$bindings[$key] = $value;
    }

    /**
     * Retrieve a value from the registry.
     *
     * @param  string $key
     */
    public static function get($key)
    {
        if (! array_key_exists($key, static::$bindings)) {
            throw new \Exception("No {$key} is bound in the registry.");
        }

        return static::$bindings[$key];
    }
}
