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

use Hateoas\Representation\PaginatedRepresentation;
use Hateoas\Representation\CollectionRepresentation;

class PaginationService
{
    // Represent the default page var
    Const DEFAULTPAGE = 1;
    // Represent limit element by page
    Const LIMITELEMENT = 3;

    public function paginationCreation(int $page, int $limit, array $list, string $route): PaginatedRepresentation
    {
        // Set offset/position for slice function in array listConsumer
        $offset = ($page - 1) * $limit;
        // Create CollectionRepresentation for pagination HateOAS function
        $listShorted = new CollectionRepresentation(\array_slice($list, $offset, $limit));

        // Set and cast to int the number of pages.
        $nbPages = (int) ceil(\count($list) / $limit);

        // Create pagination with HateOAS
        $paginatedCollection = new PaginatedRepresentation(
            $listShorted,
            $route, // route
            [], // route parameters
            $page,       // page number
            $limit,      // limit
            $nbPages,       // total pages
            'page',  // page route parameter name, optional, defaults to 'page'
            'limit', // limit route parameter name, optional, defaults to 'limit'
            false,   // generate relative URIs, optional, defaults to `false`
            \count($list)       // total collection size, optional, defaults to `null`
        );
        return $paginatedCollection;
    }
}
