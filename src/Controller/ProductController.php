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

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\CacheService;
use App\Service\PaginationService;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private CacheService $cacheService;
    private SerializerInterface $serializer;
    private PaginationService $paginationService;

    public function __construct(SerializerInterface $serializer, CacheService $cacheService, PaginationService $paginationService )
    {
        $this->serializer = $serializer;
        $this->cacheService = $cacheService;
        $this->paginationService = $paginationService;
    }

    #[Route('api/products', name: 'app_allProduct', methods: ['GET'])]
    public function getProducts(Request $request, ProductRepository $repoProduct): JsonResponse
    {
        // Get params from Request,set params for pagination and cast to int.
        $page = (int) $request->get('page', PAGINATIONSERVICE::DEFAULTPAGE);
        $limit = (int) $request->get('limit', PAGINATIONSERVICE::LIMITELEMENT);
        $route = $request->attributes->get('_route');

        // Cache
        $idCache = $this->cacheService->idCacheCreation([PRODUCT::CACHEPRODUCT, $page, $limit]);
        $listProduct = $this->cacheService->cachePoolCreation($idCache, $repoProduct, PRODUCT::CACHEPRODUCT, null);

        $paginatedCollection = $this->paginationService->paginationCreation($page, $limit, $listProduct, $route);

        $jsonProductList = $this->serializer->serialize($paginatedCollection, 'json');

        return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
    }

    #[Route('api/products/{id}', name: 'app_detailProduct', methods: ['GET'])]
    public function getDetailProduct(Product $product): JsonResponse
    {
        $jsonProduct = $this->serializer->serialize($product, 'json');

        return new JsonResponse($jsonProduct, Response::HTTP_OK, [], true);
    }
}
