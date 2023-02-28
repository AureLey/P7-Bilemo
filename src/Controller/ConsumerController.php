<?php

namespace App\Controller;

use App\Repository\ConsumerRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ConsumerController extends AbstractController
{
    #[Route('api/consumer', name: 'app_consumer')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ConsumerController.php',
        ]);
    }

    #[Route('api/consumers', name: 'app_allConsumers', methods: ['GET'])]
    public function getConsumers(ConsumerRepository $repoConsumer, SerializerInterface $serializer): JsonResponse
    {
        $consumerList = $repoConsumer->findAll();
        $jsonConsumerList = $serializer->serialize($consumerList, 'json');

        return new JsonResponse($jsonConsumerList, Response::HTTP_OK, [], true);
    }
}
