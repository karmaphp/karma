<?php namespace Karma;

use Interop\Container\ContainerInterface;
use Invoker\Invoker;
use Invoker\ParameterResolver\AssociativeArrayResolver;
use Invoker\ParameterResolver\Container\TypeHintContainerResolver;
use Invoker\ParameterResolver\DefaultValueResolver;
use Invoker\ParameterResolver\ResolverChain;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Handlers\Error;
use Slim\Handlers\NotAllowed;
use Slim\Handlers\NotFound;
use Slim\Handlers\PhpError;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\InvocationStrategyInterface;
use Slim\Interfaces\RouterInterface;
use Slim\Router;
use function DI\get;
use function DI\object;

class ContainerBuilder
{
    /**
     * @param string $containerClass
     * @param array $config
     * @param bool $useAnnotations
     *
     * @return ContainerInterface
     */
    public static function build(string $containerClass = Container::class, $config = [], $useAnnotations = true)
    {
        $builder = new \DI\ContainerBuilder($containerClass);

        $builder->useAnnotations($useAnnotations);

        $builder->addDefinitions(static::getDefaultConfig());
        $builder->addDefinitions($config);

        $container = $builder->build();

        $container->set(Request::class, get('request'));
        $container->set(Response::class, get('response'));
        $container->set($containerClass, get(\DI\Container::class));

        return $container;
    }

    private static function getDefaultConfig()
    {
        return [

            'settings.httpVersion'                       => '1.1',
            'settings.responseChunkSize'                 => 4096,
            'settings.outputBuffering'                   => 'append',
            'settings.determineRouteBeforeAppMiddleware' => false,
            'settings.displayErrorDetails'               => false,
            'settings.addContentLengthHeader'            => true,
            'settings.routerCacheFile'                   => false,

            'settings' => [
                'httpVersion'                       => get('settings.httpVersion'),
                'responseChunkSize'                 => get('settings.responseChunkSize'),
                'outputBuffering'                   => get('settings.outputBuffering'),
                'determineRouteBeforeAppMiddleware' => get('settings.determineRouteBeforeAppMiddleware'),
                'displayErrorDetails'               => get('settings.displayErrorDetails'),
                'addContentLengthHeader'            => get('settings.addContentLengthHeader'),
                'routerCacheFile'                   => get('settings.routerCacheFile'),
            ],

            'errorHandler'      => object(Error::class)
                ->constructor(get('settings.displayErrorDetails')),
            'phpErrorHandler'   => object(PhpError::class)
                ->constructor(get('settings.displayErrorDetails')),
            'notFoundHandler'   => object(NotFound::class),
            'notAllowedHandler' => object(NotAllowed::class),

            'environment' => object(Environment::class)
                ->constructor($_SERVER),

            ServerRequestInterface::class => function (ContainerInterface $container) {
                return Request::createFromEnvironment($container->get('environment'));
            },
            'request'                     => get(ServerRequestInterface::class),

            ResponseInterface::class => function (ContainerInterface $container) {
                $headers = new Headers(['Content-Type' => 'text/html; charset=utf-8']);
                $response = new Response(200, $headers);

                return $response->withProtocolVersion($container->get('settings')['httpVersion']);
            },
            'response'               => get(ResponseInterface::class),

            RouterInterface::class => function (ContainerInterface $container) {
                $router = new Router();
                $router->setCacheFile($container->get('settings.routerCacheFile'));
                $router->setContainer($container);

                return $router;
            },
            'router'               => get(RouterInterface::class),

            InvocationStrategyInterface::class => function (ContainerInterface $container) {

                $resolveChain = new ResolverChain([
                    // Inject parameters by name first
                    new AssociativeArrayResolver(),
                    // Then inject services by type-hints for those that weren't resolved
                    new TypeHintContainerResolver($container),
                    // Then fall back on parameters default values for optional route parameters
                    new DefaultValueResolver(),
                ]);

                return new CallableStrategy(new Invoker($resolveChain, $container));
            },

            'foundHandler' => get(InvocationStrategyInterface::class),

            CallableResolverInterface::class => function (ContainerInterface $container) {
                return new CallableResolver(new \Invoker\CallableResolver($container));
            },
            'callableResolver'               => get(CallableResolverInterface::class)
        ];
    }
}
