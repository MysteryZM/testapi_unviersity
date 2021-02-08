<?php

namespace App\Serializer\Normalizer;

use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ExceptionNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    private $normalizer;

    public function __construct(ObjectNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function normalize($exception, string $format = null, array $context = []): array
    {
        /** @var FlattenException $exception */
        return [
            'content' => $exception->getStatusText(),
            'exception'=> [
                'message' => $exception->getMessage(),
                'code' => $exception->getStatusCode()
            ],
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof FlattenException;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
