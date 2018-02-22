<?php namespace Karma;

use Invoker\CallableResolver as InvokerResolver;
use Slim\Interfaces\CallableResolverInterface;

/**
 * Resolve middleware and route callables using PHP-DI.
 */
class CallableResolver implements CallableResolverInterface
{
    /**
     * @var InvokerResolver
     */
    private $callableResolver;

    /**
     * CallableResolver constructor.
     *
     * @param InvokerResolver $callableResolver
     */
    public function __construct(InvokerResolver $callableResolver)
    {
        $this->callableResolver = $callableResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($toResolve)
    {
        return $this->callableResolver->resolve($toResolve);
    }
}
