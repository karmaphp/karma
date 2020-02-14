<?php namespace Karma;

use Invoker\Invoker;
use Invoker\ParameterResolver\AssociativeArrayResolver;
use Invoker\ParameterResolver\Container\TypeHintContainerResolver;
use Invoker\ParameterResolver\DefaultValueResolver;
use Invoker\ParameterResolver\ResolverChain;
use Psr\Container\ContainerInterface;
use Slim\App;

class AppFactory
{
    public static function create(?ContainerInterface $container = null): App
    {
        if (is_null($container)) {
            $container = ContainerBuilder::build();
        }

        $app = \Slim\Factory\AppFactory::createFromContainer($container);

        $container->set(App::class, $app);
        $container->set('app', $app);

        $app->getRouteCollector()->setDefaultInvocationStrategy(new ControllerInvoker(new Invoker(new ResolverChain([
            // Inject parameters by name first
            new AssociativeArrayResolver(),
            // Then inject services by type-hints for those that weren't resolved
            new TypeHintContainerResolver($container),
            // Then fall back on parameters default values for optional route parameters
            new DefaultValueResolver(),
        ]), $container)));

        return $app;
    }
}
