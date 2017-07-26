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
}
