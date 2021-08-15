<?php

declare(strict_types=1);

namespace CooarchiApp\Handler;

use CooarchiEntities\User;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Teapot\StatusCode;

class AuthStatusHandler implements RequestHandlerInterface
{
    public const ROUTE = '/auth';
    public const ROUTE_NAME = 'auth';

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        if ($request->getMethod() !== 'GET') {
            return new JsonResponse(['error' => 'Invalid request method'], 500);
        }

        $user = $request->getAttribute('identity', false);
        if ($user instanceof User) {
            $user = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'role' => $user->getRole(),
            ];

            return new JsonResponse($user, StatusCode\All::OK);
        }

        return new JsonResponse(false, StatusCode\All::NOT_FOUND);
    }
}
