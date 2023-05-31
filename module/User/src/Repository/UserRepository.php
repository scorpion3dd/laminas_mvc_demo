<?php
/**
 * This file is part of the Simple Web Demo Free Lottery Management Application.
 *
 * This project is no longer maintained.
 * The project is written in Laminas Framework Release.
 *
 * @link https://github.com/scorpion3dd
 * @author Denis Puzik <scorpion3dd@gmail.com>
 * @copyright Copyright (c) 2021-2022 scorpion3dd
 */

declare(strict_types=1);

namespace User\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use User\Entity\User;

/**
 * This is the custom repository class for User entity
 * @package User\Repository
 */
class UserRepository extends EntityRepository
{
    /**
     * Retrieves all users in ascending dateCreated order
     *
     * @return Query
     */
    public function findAllUsers(): object
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('u')
            ->from(User::class, 'u')
            ->orderBy('u.dateCreated', 'ASC');

        return $queryBuilder->getQuery();
    }

    /**
     * Retrieves all users in ascending dateCreated order
     * @param int $access
     * @param int $status
     *
     * @return Query
     */
    public function findUsersAccess(int $access = 1, int $status = 1): object
    {
        $entityManager = $this->getEntityManager();
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('u')
            ->from(User::class, 'u')
            ->where("u.access = :access")
            ->andWhere("u.status = :status")
            ->setParameter('access', $access)
            ->setParameter('status', $status)
            ->orderBy('u.id', 'ASC');

        return $queryBuilder->getQuery();
    }
}
