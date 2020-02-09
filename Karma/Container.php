<?php namespace Karma;

use ArrayAccess;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

/**
 * @property Request request
 * @property Response response
 */
class Container extends \DI\Container implements ArrayAccess
{
    public function get($name)
    {
        switch ($name) {

            case 'request':

                if ( ! $this->has($name)) {
                    return ServerRequestCreatorFactory::create()->createServerRequestFromGlobals();
                }

                break;

            case 'response':

                if ( ! $this->has($name)) {
                    return (new ResponseFactory())->createResponse();
                }

                break;
        }

        return parent::get($name);
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
