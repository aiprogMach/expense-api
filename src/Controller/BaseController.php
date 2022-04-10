<?php

declare(strict_types=1);

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class BaseController extends AbstractFOSRestController
{
    protected function toJson(mixed $data, int $status = 200, array $headers = []): JsonResponse
    {
        $json = null !== $data ? $this->container->get('jms_serializer')->serialize($data, 'json'): '';

        return new JsonResponse($json, $status, $headers, true);
    }

    protected function error(string $message, int $status = 400): JsonResponse
    {
        return $this->toJson(['error' => $message], $status);
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'jms_serializer' => SerializerInterface::class,
        ]);
    }

}
