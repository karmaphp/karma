<?php namespace Karma;

use Slim\Psr7\Request;
use Slim\Psr7\Response;

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
     * execute after __construct
     *
     * @param Request $request
     * @param Response $response
     *
     * @see ControllerInvoker
     *
     */
    public function init($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function json(array $data, $status = 200, $encodingOptions = 0, $depth = 512)
    {
        $this->response->getBody()->write(json_encode($data, $encodingOptions, $depth));

        return $this->response
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/json');
    }

    public function xml($data, $status = 200)
    {
        $this->response->getBody()->write($data);

        return $this->response
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/xml;charset=utf-8');
    }

    public function redirect($url, $status = 302)
    {
        return $this->response
            ->withHeader('Location', $url)
            ->withStatus($status);
    }

    public function is($method)
    {
        return $this->request->getMethod() === strtoupper($method);
    }

    public function isGet()
    {
        return $this->is('GET');
    }

    public function isPost()
    {
        return $this->is('POST');
    }

    public function isPut()
    {
        return $this->is('PUT');
    }

    public function isDelete()
    {
        return $this->is('DELETE');
    }

    public function param($key, $default = null)
    {
        $postParams = $this->request->getParsedBody();
        $getParams = $this->request->getQueryParams();

        if (is_array($postParams) && isset($postParams[$key])) {
            return $postParams[$key];
        }

        if (is_object($postParams) && property_exists($postParams, $key)) {
            return $postParams->$key;
        }

        if (isset($getParams[$key])) {
            return $getParams[$key];
        }

        return $default;
    }

    public function params()
    {
        $params = $this->request->getQueryParams();
        $postParams = $this->request->getParsedBody();

        if ($postParams) {
            $params = array_merge($params, (array)$postParams);
        }

        return $params;
    }
}
