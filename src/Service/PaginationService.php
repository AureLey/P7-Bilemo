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

namespace App\Service;

use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;

class PaginationService
{
    // Represent the default page var.
    public const DEFAULTPAGE = 1;
    // Represent limit element by page.
    public const LIMITELEMENT = 3;
 
    /**
     * page number, limit of elements peer page, list of elements and the route, return HateOas paginationRepresentation.
     *
     * @param  int $page
     * @param  int $limit
     * @param  array $list
     * @param  string $route
     * @return PaginatedRepresentation
     */
    public function paginationCreation(int $page, int $limit, array $list, string $route): PaginatedRepresentation
    {
        // Set offset/position for slice function in array listElement.
        $offset = ($page - 1) * $limit;
        // Create CollectionRepresentation for pagination HateOAS function.
        $listShorted = new CollectionRepresentation(\array_slice($list, $offset, $limit));

        // Set and cast to int the number of pages.
        $nbPages = (int) ceil(\count($list) / $limit);

        // Create pagination with HateOAS
        $paginatedCollection = new PaginatedRepresentation(
            $listShorted,   // CollectionRepresentation
            $route,         // Route
            [],             // Route parameters
            $page,          // Page number
            $limit,         // Limit
            $nbPages,       // Total pages
            'page',         // Page route parameter name, optional, defaults to 'page'
            'limit',        // Limit route parameter name, optional, defaults to 'limit'
            false,          // Generate relative URIs, optional, defaults to `false`
            \count($list)   // Total collection size, optional, defaults to `null`
        );

        return $paginatedCollection;
    }
}
