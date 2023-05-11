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
use App\Service\CacheService;
use OpenApi\Annotations as OA;
use App\Repository\ProductRepository;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Contracts\Cache\ItemInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Hateoas\Representation\PaginatedRepresentation;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Hateoas\Representation\CollectionRepresentation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ProductController extends AbstractController
{

    private CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    #[Route('api/products', name: 'app_allProduct', methods: ['GET'])]
    public function getProducts(
        Request $request,
        ProductRepository $repoProduct,
        SerializerInterface $serializer,
        TagAwareCacheInterface $cachePool): JsonResponse
    {
        // Get params from Request,set params for pagination and cast to int.
        $page = (int) $request->get('page', 1);
        $limit = (int) $request->get('limit', 3);

        // Cache 
        $idCache = $this->cacheService->idCacheCreation([PRODUCT::CACHEPRODUCT, $page, $limit]);
        $listProduct = $this->cacheService->cachePoolCreation($idCache, $repoProduct, PRODUCT::CACHEPRODUCT, null);

        // Set offset/position for slice function in array listProduct
        $offset = ($page - 1) * $limit;

        // Create CollectionRepresentation for pagination HateOAS function
        $listProductShorted = new CollectionRepresentation(\array_slice($listProduct, $offset, $limit));

        // Set and cast to int the number of pages.
        $nbPages = (int) ceil(\count($listProduct) / $limit);

        // Create pagination with HateOAS
        $paginatedCollection = new PaginatedRepresentation(
            $listProductShorted,
            'app_allProduct', // route
            [], // route parameters
            $page,       // page number
            $limit,      // limit
            $nbPages,       // total pages
            'page',  // page route parameter name, optional, defaults to 'page'
            'limit', // limit route parameter name, optional, defaults to 'limit'
            false,   // generate relative URIs, optional, defaults to `false`
            \count($listProduct)       // total collection size, optional, defaults to `null`
        );

        $jsonProductList = $serializer->serialize($paginatedCollection, 'json');

        return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
    }

    #[Route('api/products/{id}', name: 'app_detailProduct', methods: ['GET'])]
    public function getDetailProduct(Product $product, SerializerInterface $serializer): JsonResponse
    {
        $jsonProduct = $serializer->serialize($product, 'json');

        return new JsonResponse($jsonProduct, Response::HTTP_OK, [], true);
    }
}
