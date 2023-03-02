<?php

declare(strict_types=1);

/*
 * This file is part of Bilemo
 *
 * (c)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\User;
use App\Entity\Consumer;
use App\Repository\ConsumerRepository;
use JMS\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ConsumerController extends AbstractController
{
    #[Route('api/consumers', name: 'app_allConsumers', methods: ['GET'])]
    public function getConsumers(ConsumerRepository $repoConsumer, SerializerInterface $serializer): JsonResponse
    {
        $consumerList = $repoConsumer->findAll();
        $context = SerializationContext::create()->setGroups(['getConsumers']);
        $jsonConsumerList = $serializer->serialize($consumerList, 'json', $context);

        return new JsonResponse($jsonConsumerList, Response::HTTP_OK, [], true);
    }

    #[Route('api/consumers/{id}', name: 'app_detailConsumer', methods: ['GET'])]
    public function getDetailConsumer(Consumer $consumer, SerializerInterface $serializer): JsonResponse
    {
        $context = SerializationContext::create()->setGroups(['getConsumers']);
        $jsonConsumer = $serializer->serialize($consumer, 'json', $context);

        return new JsonResponse($jsonConsumer, Response::HTTP_OK, [], true);
    }

    #[Route('api/consumers/{id}', name: 'app_deleteConsumer', methods: ['DELETE'])]
    public function deleteConsumer(Consumer $consumer, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($consumer);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('api/consumers', name: 'app_creationConsumer', methods: ['POST'])]
    public function creationConsumer(
        Request $request,
        SerializerInterface $serialiazer,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $consumer = $serialiazer->deserialize($request->getContent(), Consumer::class, 'json');
        $user = $this->getUser();
        $consumer->setUser($user);
        $entityManager->persist($consumer);
        $entityManager->flush();

        $context = SerializationContext::create()->setGroups(['getConsumers']);
        $jsonConsumer = $serialiazer->serialize($consumer, 'json', $context);

        $location = $urlGenerator->generate('app_detailConsumer', ['id' => $consumer->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonConsumer, Response::HTTP_CREATED, ["Location"=> $location], true);
    }


    #[Route('api/consumers/{id}', name: 'app_updateConsumer', methods: ['PUT'])]
    public function updateConsumer(
        Consumer $currentConsumer,
        Request $request, 
        SerializerInterface $serializer, 
        EntityManagerInterface $entityManager): JsonResponse
    {
        $newConsumer = $serializer->deserialize($request->getContent(),Consumer::class,'json');

        $currentConsumer->setLastname($newConsumer->getLastname())
                        ->setFirstname($newConsumer->getFirstname());
        

        $entityManager->persist($currentConsumer);
        $entityManager->flush();
        $context = SerializationContext::create()->setGroups(['getConsumers']);
        $jsonConsumer = $serializer->serialize($currentConsumer, 'json', $context);

        return new JsonResponse($jsonConsumer, Response::HTTP_NO_CONTENT);
    }
}
