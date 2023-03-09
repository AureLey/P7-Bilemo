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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route('api/products', name: 'app_allProduct', methods: ['GET'])]
    public function getProducts(Request $request, ProductRepository $repoProduct, SerializerInterface $serializer): JsonResponse
    {
        // Set params for pagination
        $page = $request->get('page',1);
        $limit = $request->get('limit', 3);
        //Call Custom function in repo with pagination
        $productList = $repoProduct->findAllWithPagination($page,$limit);
        
        $jsonProductList = $serializer->serialize($productList, 'json');

        return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
    }

    #[Route('api/products/{id}', name: 'app_detailProduct', methods: ['GET'])]
    public function getDetailProduct(Product $product, SerializerInterface $serializer): JsonResponse
    {
        $jsonProduct = $serializer->serialize($product, 'json');

        return new JsonResponse($jsonProduct, Response::HTTP_OK, [], true);
    }
}
