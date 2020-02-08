<?php namespace Karma;

use Slim\Interfaces\CallableResolverInterface;

/**
 * Resolve middleware and route callables using PHP-DI.
 */
class CallableResolver implements CallableResolverInterface
{
    /**
     * @var \Invoker\CallableResolver
     */
    private $resolver;

    /**
     * CallableResolver constructor.
     *
     * @param \Invoker\CallableResolver $resolver
     */
    public function __construct(\Invoker\CallableResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function resolve($toResolve): callable
    {
        return $this->resolver->resolve($toResolve);
    }
}
