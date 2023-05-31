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

namespace User\Service;

use Application\Service\RbacAssertionManager;
use Doctrine\ORM\EntityManager;
use Exception;
use User\Entity\Permission;
use Laminas\Authentication\AuthenticationService;
use Laminas\Cache\Storage\StorageInterface;
use Laminas\Log\Logger;
use Laminas\Permissions\Rbac\Rbac;
use User\Entity\User;
use User\Entity\Role;

/**
 * This service is responsible for initialzing RBAC (Role-Based Access Control)
 * Class RbacManager
 * @package User\Service
 */
class RbacManager
{
    /** @var EntityManager $entityManager */
    private EntityManager $entityManager;

    /** @var Rbac|null $rbac */
    private ?Rbac $rbac;

    /** @var AuthenticationService $authService */
    private AuthenticationService $authService;

    /** @var StorageInterface $cache */
    private StorageInterface $cache;

    /** @var array $assertionManagers */
    private array $assertionManagers = [];

    /** @var Logger $logger */
    protected Logger $logger;

    /**
     * RbacManager constructor
     * @param Logger $logger
     * @param EntityManager $entityManager
     * @param AuthenticationService $authService
     * @param StorageInterface $cache
     * @param array $assertionManagers
     */
    public function __construct(
        Logger $logger,
        EntityManager $entityManager,
        AuthenticationService $authService,
        StorageInterface $cache,
        array $assertionManagers
    ) {
        $this->entityManager = $entityManager;
        $this->authService = $authService;
        $this->cache = $cache;
        $this->assertionManagers = $assertionManagers;
        $this->logger = $logger;
    }

    /**
     * @param bool $forceCreate
     *
     * @return void
     */
    public function init(bool $forceCreate = false): void
    {
        if (! empty($this->rbac) && ! $forceCreate) {
            return;
        }
        if ($forceCreate) {
            $this->cache->removeItem('rbac_container');
        }
        $result = false;
        $this->rbac = $this->cache->getItem('rbac_container', $result);
        if (! $result) {
            $rbac = new Rbac();
            $this->rbac = $rbac;
            $rbac->setCreateMissingRoles(true);
            $roles = $this->entityManager->getRepository(Role::class)->findBy([], ['id' => 'ASC']);
            /** @var Role $role */
            foreach ($roles as $role) {
                $roleName = $role->getName();
                $parentRoleNames = [];
                /** @var Role $parentRole */
                foreach ($role->getParentRoles() as $parentRole) {
                    $parentRoleNames[] = $parentRole->getName();
                }
                $rbac->addRole($roleName, $parentRoleNames);
                /** @var Permission $permission */
                foreach ($role->getPermissions() as $permission) {
                    $rbac->getRole($roleName)->addPermission($permission->getName());
                }
            }
            $this->cache->setItem('rbac_container', $rbac);
        }
    }

    /**
     * Checks whether the given user has permission
     * @param User|null $user
     * @param string $permission
     * @param array|null $params
     *
     * @return bool
     * @throws Exception
     */
    public function isGranted(?User $user, string $permission, ?array $params = null): bool
    {
        if (empty($this->rbac)) {
            $this->init();
        }
        if (empty($user)) {
            $identity = $this->authService->getIdentity();
            if (empty($identity)) {
                return false;
            }
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $identity]);
            if (empty($user)) {
                $message = 'There is no user with such identity';
                $this->logger->log(Logger::ERR, $message);
                throw new Exception($message);
            }
        }
        /** @var User $user */
        $roles = $user->getRoles();
        foreach ($roles as $role) {
            if ($this->rbac->isGranted($role->getName(), $permission)) {
                if (empty($params)) {
                    return true;
                }
                /** @var RbacAssertionManager $assertionManager */
                foreach ($this->assertionManagers as $assertionManager) {
                    if ($assertionManager->assert($this->rbac, $permission, $params)) {
                        return true;
                    }
                }
            }
            $parentRoles = $role->getParentRoles();
            foreach ($parentRoles as $parentRole) {
                if ($this->rbac->isGranted($parentRole->getName(), $permission)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return AuthenticationService
     */
    public function getAuthService(): AuthenticationService
    {
        return $this->authService;
    }

    /**
     * @param Rbac|null $rbac
     *
     * @return $this
     */
    public function setRbac(?Rbac $rbac): self
    {
        $this->rbac = $rbac;

        return $this;
    }
}
