<?php namespace Karma;

use Slim\Http\Request;
use Slim\Http\Response;

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
     * @see CallableStrategy
     *
     * @param Request $request
     * @param Response $response
     */
    public function init($request, $response)
    {
        $this->request = $request;
        $this->response = $response;

        $this->c()->set('request', $request);
        $this->c()->set('response', $response);
    }

    /**
     * @param array $data The data
     * @param int $status The HTTP status code.
     * @param int $encodingOptions
     *
     * @return Response
     */
    public function json(array $data, $status = 200, $encodingOptions = 0)
    {
        return $this->response->withJson($data, $status, $encodingOptions);
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
        $body = $this->response->getBody();
        $body->rewind();
        $body->write($data);

        $response = $this->response->withHeader('Content-Type', 'application/xml;charset=utf-8');

        if (isset($status)) {
            return $response->withStatus($status);
        }

        return $response;
    }

}
