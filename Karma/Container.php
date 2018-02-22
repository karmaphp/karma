<?php namespace Karma;

use DI\ContainerBuilder;
use Invoker\Invoker;
use Invoker\ParameterResolver\AssociativeArrayResolver;
use Invoker\ParameterResolver\Container\TypeHintContainerResolver;
use Invoker\ParameterResolver\DefaultValueResolver;
use Invoker\ParameterResolver\ResolverChain;
use Psr\Container\ContainerInterface;
use Slim\Handlers\Error;
use Slim\Handlers\NotAllowed;
use Slim\Handlers\NotFound;
use Slim\Handlers\PhpError;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;
use function DI\create;
use function DI\get;

class Container extends \DI\Container implements \ArrayAccess
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

        $builder->addDefinitions(static::getDefaultConfig());
        $builder->addDefinitions($config);

        return $builder->build();
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

            'settings'          => [
                'httpVersion'                       => get('settings.httpVersion'),
                'responseChunkSize'                 => get('settings.responseChunkSize'),
                'outputBuffering'                   => get('settings.outputBuffering'),
                'determineRouteBeforeAppMiddleware' => get('settings.determineRouteBeforeAppMiddleware'),
                'displayErrorDetails'               => get('settings.displayErrorDetails'),
                'addContentLengthHeader'            => get('settings.addContentLengthHeader'),
                'routerCacheFile'                   => get('settings.routerCacheFile'),
            ],

            // Default Slim services
            'router'            => create(Router::class)
                ->method('setCacheFile', get('settings.routerCacheFile')),
            Router::class       => get('router'),
            'errorHandler'      => create(Error::class)
                ->constructor(get('settings.displayErrorDetails')),
            'phpErrorHandler'   => create(PhpError::class)
                ->constructor(get('settings.displayErrorDetails')),
            'notFoundHandler'   => create(NotFound::class),
            'notAllowedHandler' => create(NotAllowed::class),
            'environment'       => function () {
                return new Environment($_SERVER);
            },
            'request'           => function (ContainerInterface $c) {
                return Request::createFromEnvironment($c->get('environment'));
            },
            'response'          => function (ContainerInterface $c) {
                $headers = new Headers(['Content-Type' => 'text/html; charset=UTF-8']);
                $response = new Response(200, $headers);

                return $response->withProtocolVersion($c->get('settings')['httpVersion']);
            },
            'foundHandler'      => create(ControllerInvoker::class)
                ->constructor(get('foundHandler.invoker')),

            'foundHandler.invoker' => function (ContainerInterface $c) {
                $resolvers = [
                    // Inject parameters by name first
                    new AssociativeArrayResolver,
                    // Then inject services by type-hints for those that weren't resolved
                    new TypeHintContainerResolver($c),
                    // Then fall back on parameters default values for optional route parameters
                    new DefaultValueResolver(),
                ];

                return new Invoker(new ResolverChain($resolvers), $c);
            },

            'callableResolver' => create(CallableResolver::class),

            ContainerInterface::class => get(\DI\Container::class),
        ];
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    public function __isset($name)
    {
        return $this->has($name);
    }

    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->set($offset, null);
    }
}
