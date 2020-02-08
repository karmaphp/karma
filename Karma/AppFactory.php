<?php namespace Karma;

use DI\ContainerBuilder;
use Invoker\Invoker;
use Invoker\ParameterResolver\AssociativeArrayResolver;
use Invoker\ParameterResolver\Container\TypeHintContainerResolver;
use Invoker\ParameterResolver\DefaultValueResolver;
use Invoker\ParameterResolver\ResolverChain;
use Slim\App;
use Slim\Interfaces\CallableResolverInterface;

class AppFactory
{
    public static function create($class = Container::class, $config = [], $useAnnotations = true): App
    {
        $container = (new ContainerBuilder($class))
            ->useAnnotations($useAnnotations)
            ->addDefinitions($config)
            ->build();

        $container->set(CallableResolverInterface::class, new CallableResolver(new \Invoker\CallableResolver($container)));

        $app = \Slim\Factory\AppFactory::createFromContainer($container);

        $container->set(App::class, $app);

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
