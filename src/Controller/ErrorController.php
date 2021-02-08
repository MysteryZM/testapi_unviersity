<?php

namespace App\Controller;

use App\Serializer\Normalizer\ExceptionNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ErrorController extends AbstractController
{
    /**
     * @param FlattenException $exception
     *
     * @return Response
     *
     * @throws ExceptionInterface
     */
    public function index(FlattenException $exception): Response
    {
        $objectNormalizer = new ObjectNormalizer();
        $normalizers = [new ExceptionNormalizer($objectNormalizer)];
        $serializer = new Serializer($normalizers);
        return $this->json($serializer->normalize($exception));
    }
}
