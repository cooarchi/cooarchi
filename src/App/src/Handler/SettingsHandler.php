<?php

declare(strict_types=1);

namespace CooarchiApp\Handler;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Teapot\StatusCode;

class SettingsHandler implements RequestHandlerInterface
{
    public const ROUTE = '/settings';
    public const ROUTE_NAME = 'settings';

    /**
     * @var array
     */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        if ($request->getMethod() !== 'GET') {
            return new JsonResponse(['error' => 'Invalid request method'], 500);
        }

        return new JsonResponse($this->config, StatusCode\All::OK);
    }
}
