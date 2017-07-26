<?php namespace Karma;

use Interop\Container\ContainerInterface;

class App extends \Slim\App
{
    /**
     * @param ContainerInterface|null $container
     */
    public function __construct($container = null)
    {
        if (is_null($container)) {
            $container = Container::build();
        }

        parent::__construct($container);
    }
}
