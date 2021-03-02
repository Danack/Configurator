<?php

declare(strict_types = 1);

namespace ConfiguratorTest;

class ExampleConfig
{
    const FOO = 'configurator.foo';

    const BAR = 'configurator.bar';

    const DB = 'configurator.db';

    /**
     * @param $key
     * @return mixed
     * @throws \Exception
     */
    public static function getConfig($key)
    {
        static $values = null;
        if ($values === null) {
            $values = getGeneratedConfig();
        }

        if (array_key_exists($key, $values) == false) {
            throw new \Exception("No value for " . $key);
        }

        return $values[$key];
    }
}
