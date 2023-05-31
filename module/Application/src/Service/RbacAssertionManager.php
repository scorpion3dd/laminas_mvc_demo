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

namespace Application\Service;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\Permissions\Rbac\Rbac;
use User\Entity\User;

/**
 * This service is used for invoking user-defined RBAC dynamic assertions
 * Class RbacAssertionManager
 * @package Application\Service
 */
class RbacAssertionManager extends AbstractService
{
    /** @var AuthenticationService $authService */
    private AuthenticationService $authService;

    /**
     * RbacAssertionManager constructor
     * @param ContainerInterface $container
     * @param EntityManager $entityManager
     * @param AuthenticationService $authService
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container, EntityManager $entityManager, AuthenticationService $authService)
    {
        parent::__construct($container);
        $this->entityManager = $entityManager;
        $this->authService = $authService;
    }

    /**
     * This method is used for dynamic assertions
     * @param Rbac $rbac
     * @param string $permission
     * @param array $params
     *
     * @return bool
     */
    public function assert(Rbac $rbac, string $permission, array $params): bool
    {
        /** @var User $currentUser */
        $currentUser = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => $this->authService->getIdentity()]);

        return ($permission == 'profile.own.view' && $params['user']->getId() == $currentUser->getId());
    }
}
