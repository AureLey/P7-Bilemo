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

use App\Entity\Consumer;
use App\Repository\ConsumerRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ConsumerController extends AbstractController
{
    /**
     * getConsumers return all Consumers to the Client/User, FindBy( User_id).
     */
    #[Route('api/consumers', name: 'app_allConsumers', methods: ['GET'])]
    public function getConsumers(Request $request, ConsumerRepository $repoConsumer, SerializerInterface $serializer): JsonResponse
    {
        $page = $request->get('page',1);
        $limit = $request->get('limit', 3);
        // Get all consumers from the logged User        
        $consumerList = $repoConsumer->findAllWithPagination($this->getUser(),$page,$limit);
        // Create a group to cancel circule errors and get User in Consumer
        $context = SerializationContext::create()->setGroups(['getConsumers']);
        $jsonConsumerList = $serializer->serialize($consumerList, 'json', $context);

        return new JsonResponse($jsonConsumerList, Response::HTTP_OK, [], true);
    }

    /**
     * getDetailConsumer return one Consumer informations, control by is_granted and Voter.
     */
    #[Route('api/consumers/{id}', name: 'app_detailConsumer', methods: ['GET'])]
    #[Security("is_granted('VIEW', consumer)", statusCode: 403, message: 'Forbidden-Resource not found.')]
    public function getDetailConsumer(Consumer $consumer, SerializerInterface $serializer): JsonResponse
    {
        // Create a group to cancel circule errors and get User in Consumer
        $context = SerializationContext::create()->setGroups(['getConsumers']);
        $jsonConsumer = $serializer->serialize($consumer, 'json', $context);

        return new JsonResponse($jsonConsumer, Response::HTTP_OK, [], true);
    }

    /**
     * updateConsumer. modified Consumer informations, control by is_granted and Voter.
     */
    #[Route('api/consumers/{id}', name: 'app_updateConsumer', methods: ['PATCH', 'PUT'])]
    #[Security("is_granted('EDIT', currentConsumer)", statusCode: 403, message: 'Forbidden-Resource not found.')]
    public function updateConsumer(
        Consumer $currentConsumer,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator): JsonResponse
    {
        // Create newConsummer with new values
        $newConsumer = $serializer->deserialize($request->getContent(), Consumer::class, 'json');

        // Modify currentConsumer with new values from newConsummer
        $currentConsumer->setLastname($newConsumer->getLastname())
                        ->setFirstname($newConsumer->getFirstname());

        // Checking errors
        $errors = $validator->validate($currentConsumer);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        // EntityManager
        $entityManager->persist($currentConsumer);
        $entityManager->flush();

        // Create a group to cancel circule errors and get User in Consumer
        $context = SerializationContext::create()->setGroups(['getConsumers']);
        $jsonConsumer = $serializer->serialize($currentConsumer, 'json', $context);

        return new JsonResponse($jsonConsumer, Response::HTTP_NO_CONTENT);
    }

    /**
     * deleteConsumer. Delete one Consumer from User, control by is_granted and Voter.
     */
    #[Route('api/consumers/{id}', name: 'app_deleteConsumer', methods: ['DELETE'])]
    #[Security("is_granted('DELETE', consumer)", statusCode: 403, message: 'Forbidden-Resource not found.')]
    public function deleteConsumer(Consumer $consumer, EntityManagerInterface $entityManager): JsonResponse
    {
        // EntityManager
        $entityManager->remove($consumer);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * creationConsumer. Create one Consumer, control by is_granted and Voter.
     */
    #[Route('api/consumers', name: 'app_creationConsumer', methods: ['POST'])]
    public function creationConsumer(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator): JsonResponse
    {
        // Deserialize infos from the request, create Consumer and setUser is logged User
        $consumer = $serializer->deserialize($request->getContent(), Consumer::class, 'json');
        $user = $this->getUser();
        $consumer->setUser($user);

        // Checking errors
        $errors = $validator->validate($consumer);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        // EntityManager
        $entityManager->persist($consumer);
        $entityManager->flush();

        // Create a group to cancel circule errors and get User in Consumer
        $context = SerializationContext::create()->setGroups(['getConsumers']);
        $jsonConsumer = $serializer->serialize($consumer, 'json', $context);

        // Create link with the new Consumer
        $location = $urlGenerator->generate('app_detailConsumer', ['id' => $consumer->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonConsumer, Response::HTTP_CREATED, ['Location' => $location], true);
    }
}
