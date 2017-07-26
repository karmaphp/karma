<?php namespace Karma;

use Slim\Http\Request;
use Slim\Http\Response;

abstract class Controller
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
     * @see ControllerInvoker
     *
     * @param Request $request
     * @param Response $response
     */
    public function setRequestResponse($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
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
