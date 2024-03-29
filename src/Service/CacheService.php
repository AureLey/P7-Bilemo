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

use App\Entity\CustomerUser;
use Doctrine\ORM\EntityRepository;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

/**
 * CacheService, this class manage cache for product and consumer.
 */
class CacheService
{
    private TagAwareCacheInterface $cachePool;

    public function __construct(TagAwareCacheInterface $cachePool)
    {
        $this->cachePool = $cachePool;
    }
    

    /**
     * create id for cache, represent the request with this informations.
     */
    public function idCacheCreation(array $args): string
    {
        return implode('', $args); // Join array elements with a string and create unique tag
    }

   
    /**
     * cachePoolCreation
     *
     * @param  string $idCache
     * @param  EntityRepository $repository
     * @param  string $tag
     * @param  CustomerUser $userId
     * @return array
     */
    public function cachePoolCreation(string $idCache, EntityRepository $repository, string $tag, ?CustomerUser $userId): array
    {
        $this->cachePool->get($idCache, function (ItemInterface $item) use ($tag) {
            $item->tag($tag);
            // echo ("debug- N'est pas dans le cache");
        }
        );
        // Testing if the paramter userid is required, to get all Consumers from the connected CustomerUser
        if (null !== $userId) {
            return $repository->findAllById($userId);
        }

        return $repository->findAll();
    }
 
    /**
     * idDeleting delete tag in cache
     *
     * @param  array $tag
     * @return void
     */
    public function idDeleting(array $tag)
    {
        $this->cachePool->invalidateTags($tag);
    }
}
