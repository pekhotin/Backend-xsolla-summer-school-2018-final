<?php

namespace App\Service;

class CustomHandler
{
    public function __invoke($request, $response, $exception)
    {
        error_log($exception->getMessage());
        return $response
            ->withStatus($exception->getCode())
            ->withHeader('Content-Type', 'application/json')
            ->withJson([
                'error' => $exception->getMessage()
            ]);
    }
}