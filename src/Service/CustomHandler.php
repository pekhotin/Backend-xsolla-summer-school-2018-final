<?php

namespace App\Service;

class CustomHandler
{
    public function __invoke($request, $response, $exception)
    {
        $code = 500;
        $message = 'System error';
        if ($exception->getCode() === 400 || $exception->getCode() === 404) {
            $code = $exception->getCode();
            $message = $exception->getMessage();
        }
        error_log($exception->getMessage());
        return $response
            ->withStatus($code)
            ->withHeader('Content-Type', 'application/json')
            ->withJson([
                'error' => $message
            ]);
    }
}
