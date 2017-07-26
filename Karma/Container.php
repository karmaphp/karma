<?php namespace Karma;

use DI\ContainerBuilder;

class Container extends \DI\Container
{
    /**
     * @param array $config
     * @param bool $useAnnotations
     *
     * @return static
     */
    public static function build($config = [], $useAnnotations = true)
    {
        $builder = new ContainerBuilder(static::class);

        $builder->useAnnotations($useAnnotations);

        $builder->addDefinitions(
            array_merge(
                require_once __DIR__ . '/config.php',
                $config
            )
        );

        return $builder->build();
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }
}
