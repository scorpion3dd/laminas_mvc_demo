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

use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use User\Entity\Permission;
use Laminas\Log\Logger;

/**
 * This service is responsible for adding/editing permissions
 * Class PermissionManager
 * @package User\Service
 */
class PermissionManager
{
    /** @var EntityManager $entityManager */
    private EntityManager $entityManager;

    /** @var RbacManager $rbacManager */
    private RbacManager $rbacManager;

    /** @var Logger $logger */
    protected Logger $logger;

    /**
     * PermissionManager constructor
     * @param EntityManager $entityManager
     * @param RbacManager $rbacManager
     * @param Logger $logger
     */
    public function __construct(EntityManager $entityManager, RbacManager $rbacManager, Logger $logger)
    {
        $this->entityManager = $entityManager;
        $this->rbacManager = $rbacManager;
        $this->logger = $logger;
    }

    /**
     * @param array $data
     *
     * @return void
     * @throws Exception
     */
    public function addPermission(array $data): void
    {
        $existingPermission = $this->entityManager->getRepository(Permission::class)->findOneByName($data['name']);
        if ($existingPermission != null) {
            $message = 'Permission with such name already exists';
            $this->logger->log(Logger::ERR, $message);
            throw new Exception($message);
        }
        $permission = new Permission();
        $permission->setName($data['name']);
        $permission->setDescription($data['description']);
        $permission->setDateCreated(Carbon::now());
        $this->entityManager->persist($permission);
        $this->entityManager->flush();
        $this->reloadRBACContainer();
    }

    /**
     * @param Permission $permission
     * @param array $data
     *
     * @return void
     * @throws Exception
     */
    public function updatePermission(Permission $permission, array $data): void
    {
        $existingPermission = $this->entityManager->getRepository(Permission::class)->findOneByName($data['name']);
        if ($existingPermission != null && $existingPermission != $permission) {
            $message = 'Another permission with such name already exists';
            $this->logger->log(Logger::ERR, $message);
            throw new Exception($message);
        }
        $permission->setName($data['name']);
        $permission->setDescription($data['description']);
        $this->entityManager->flush();
        $this->reloadRBACContainer();
    }

    /**
     * @param Permission $permission
     *
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deletePermission(Permission $permission): void
    {
        $this->entityManager->remove($permission);
        $this->entityManager->flush();
        $this->reloadRBACContainer();
    }

    /**
     * This method creates the default set of permissions if no permissions exist at all
     *
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createDefaultPermissionsIfNotExist(): void
    {
        $permission = $this->entityManager->getRepository(Permission::class)->findOneBy([]);
        if ($permission != null) {
            return;
        }
        $defaultPermissions = [
            'user.manage' => 'Manage users',
            'permission.manage' => 'Manage permissions',
            'role.manage' => 'Manage roles',
            'profile.any.view' => 'View anyone\'s profile',
            'profile.own.view' => 'View own profile',
        ];
        foreach ($defaultPermissions as $name => $description) {
            $permission = new Permission();
            $permission->setName($name);
            $permission->setDescription($description);
            $permission->setDateCreated(Carbon::now());
            $this->entityManager->persist($permission);
        }
        $this->entityManager->flush();
        $this->reloadRBACContainer();
    }

    /**
     * @return void
     */
    private function reloadRBACContainer(): void
    {
        $this->rbacManager->init(true);
    }
}
