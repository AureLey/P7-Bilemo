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
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class ProductController extends AbstractController
{   
    
    /**
     * GET ALL - getProducts     
     */
    #[Route('api/products', name: 'app_allProduct', methods: ['GET'])]    
    
    public function getProducts(
        Request $request,
        ProductRepository $repoProduct,
        SerializerInterface $serializer,
        TagAwareCacheInterface $cachePool): JsonResponse
    {
        // Set params for pagination
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);

        $idCache = 'getProducts-'.$page.'-'.$limit;
        $productList = $cachePool->get($idCache, function (ItemInterface $item) use ($repoProduct, $page, $limit) {
            // DEBUG
            echo "N'est pas dans le cache";
            $item->tag('productsCache');
            // Get all products
            return $repoProduct->findAllWithPagination($page, $limit);
        });

        $jsonProductList = $serializer->serialize($productList, 'json');

        return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
    }

    /**
     * GET - getDetailProduct    
     */
    #[Route('api/products/{id}', name: 'app_detailProduct', methods: ['GET'])]  
    public function getDetailProduct(Product $product, SerializerInterface $serializer): JsonResponse
    {
        $jsonProduct = $serializer->serialize($product, 'json');

        return new JsonResponse($jsonProduct, Response::HTTP_OK, [], true);
    }
}
