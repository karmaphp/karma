<?php namespace Karma;

use Slim\Psr7\Request;
use Slim\Psr7\Response;

abstract class Controller implements ContaineredInterface
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * execute after __construct
     *
     * @param Request $request
     * @param Response $response
     * @see ControllerInvoker
     *
     */
    public function init($request, $response)
    {
        $this->request = $request;
        $this->response = $response;

        $this->getContainer()->set('request', $request);
        $this->getContainer()->set('response', $response);
    }

    /**
     * @param array $data The data
     * @param int $status The HTTP status code.
     * @param int $encodingOptions
     * @param int $depth
     *
     * @return Response
     */
    public function json(array $data, $status = 200, $encodingOptions = 0, $depth = 512)
    {
        $this->response->getBody()->write(json_encode($data, $encodingOptions, $depth));

        return $this->response
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/json');
    }

    /**
     * Xml.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * This method prepares the response object to return an HTTP Xml
     * response to the client.
     *
     * @param mixed $data The data
     * @param int $status The HTTP status code.
     * @return Response
     */
    public function xml($data, $status = 200)
    {
        $this->response->getBody()->write($data);

        return $this->response
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/xml;charset=utf-8');
    }
}
