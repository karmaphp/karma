<?php namespace Karma\Tests;

use DI\NotFoundException;
use Karma\AppFactory;
use Karma\Container;
use Karma\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\App;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Routing\RouteCollector;
use Slim\Routing\RouteResolver;

final class ContainerTest extends TestCase
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var App
     */
    private $app;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = ContainerBuilder::build();

        $this->app = AppFactory::create($this->container);
    }

    public function testRequest()
    {
        $this->assertInstanceOf(
            Request::class,
            $this->container->get('request')
        );
    }

    public function testResponse()
    {
        $this->assertInstanceOf(
            Response::class,
            $this->container->get('response')
        );
    }

    /**
     * Test `get()` returns custum item
     */
    public function testCustomItem()
    {
        $container = ContainerBuilder::build(Container::class, [
            'DB_NAME' => 'testdb'
        ]);
        $this->assertEquals('testdb', $container->get('DB_NAME'));
    }

    /**
     * Test `get()` returns overrided item
     */
    public function testOverridedItem()
    {
        $container = ContainerBuilder::build(Container::class, [
            'settings.httpVersion' => '1.2'
        ]);
        $this->assertEquals('1.2', $container->get('settings.httpVersion'));
    }

    /**
     * Test `get()` throws error if item does not exist
     */
    public function testGetWithValueNotFoundError()
    {
        $this->expectException(NotFoundException::class);

        $this->container->get('foo');
    }

    /**
     * Test `get()` throws something that is a ContainerExpception - typically a NotFoundException, when there is a DI
     * config error
     */
    public function testGetWithDiConfigErrorThrownAsContainerValueNotFoundException()
    {
        $this->expectException(NotFoundException::class);

        $container = ContainerBuilder::build();
        $container['foo'] = function (ContainerInterface $container) {
            return $container->get('doesnt-exist');
        };
        $container->get('foo');
    }

    /**
     * Test `get()` recasts \InvalidArgumentException as ContainerInterop-compliant exceptions when an error is present
     * in the DI config
     */
    public function testGetWithDiConfigErrorThrownAsInvalidArgumentException()
    {
        $this->expectException(NotFoundException::class);

        $container = ContainerBuilder::build();
        $container['foo'] = function (ContainerInterface $container) {
            return $container['doesnt-exist'];
        };
        $container->get('foo');
    }

    /**
     * Test `get()` does not recast exceptions which are thrown in a factory closure
     */
    public function testGetWithErrorThrownByFactoryClosure()
    {
        $this->expectException(\InvalidArgumentException::class);

        $invokable = $this->getMockBuilder('StdClass')->setMethods(['__invoke'])->getMock();

        /** @var Callable $invokable */
        $invokable->expects($this->any())
            ->method('__invoke')
            ->will($this->throwException(new \InvalidArgumentException()));

        $container = ContainerBuilder::build();
        $container['foo'] = function (ContainerInterface $container) use ($invokable) {
            call_user_func($invokable);
        };
        $container->get('foo');
    }

    /**
     * Test container has request
     */
    public function testGetRequest()
    {
        $this->assertInstanceOf(RequestInterface::class, $this->container['request']);
    }

    /**
     * Test container has response
     */
    public function testGetResponse()
    {
        $this->assertInstanceOf(ResponseInterface::class, $this->container['response']);
    }

    /**
     * Test container has router
     */
    public function testGetRouter()
    {
        $this->assertInstanceOf(RouteResolver::class, $this->app->getRouteResolver());
    }

    //Test __isset
    public function testMagicIssetMethod()
    {
        $this->assertEquals(false, $this->container->__isset('notfoundkey'));
    }

    //test __get
    public function testMagicGetMethod()
    {
        $this->container->set('settings.httpVersion', '1.2');
        $this->assertSame('1.2', $this->container->__get('settings.httpVersion'));
    }

    public function testRouteCacheDisabledByDefault()
    {
        $this->assertNull($this->app->getRouteCollector()->getCacheFile());
    }
}
