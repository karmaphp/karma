<?php

use Karma\Container;
use Karma\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

final class ContainerTest extends TestCase
{
    /**
     * @var Container
     */
    private $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = ContainerBuilder::build();
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
}
