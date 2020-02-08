<?php namespace Karma;

use Slim\Interfaces\CallableResolverInterface;

class ContainerBuilder
{
    public static function build($class = Container::class, $config = [], $useAnnotations = true)
    {
        $container = (new \DI\ContainerBuilder($class))
            ->useAnnotations($useAnnotations)
            ->addDefinitions($config)
            ->build();

        $container->set(CallableResolverInterface::class, new CallableResolver(new \Invoker\CallableResolver($container)));

        return $container;
    }
}
