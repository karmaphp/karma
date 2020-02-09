<?php namespace Karma;

class ErrorHandler extends \Slim\Handlers\ErrorHandler
{
    public function json($data, int $code = 200, string $reasonPhrase = '')
    {
        $response = $this->responseFactory->createResponse($code);

        $response->getBody()->write(json_encode($data));

        return $response->withHeader('Content-Type', 'application/json');
    }
}
