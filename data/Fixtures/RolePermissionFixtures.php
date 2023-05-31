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

namespace Fixtures;

use Application\Command\AbstractCommand;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use User\Entity\Permission;
use User\Entity\Role;
use User\Entity\RolePermission;

/**
 * Auto-generated Role Permission Fixtures
 * @package Fixtures
 */
class RolePermissionFixtures extends AbstractFixtures implements FixtureInterface
{
    /**
     * PermissionFixtures construct
     * @param AbstractCommand $command
     *
     * @throws Exception
     */
    public function __construct(AbstractCommand $command)
    {
        parent::__construct();
        $this->command = $command;
    }

    /**
     * @param ObjectManager $manager
     *
     * @return void
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $count = 0;
        /** @var Role|null $roleAdministrator */
        $roleAdministrator = $manager->getRepository(Role::class)->findOneBy(['name' => 'Administrator']);
        if (empty($roleAdministrator)) {
            throw new Exception('Administrator role doesn\'t exist');
        }

        /** @var Permission|null $permission */
        $permission = $manager->getRepository(Permission::class)->findOneBy(['name' => 'permission.manage']);
        if (empty($permission)) {
            throw new Exception('Permission permission.manage doesn\'t exist');
        }
        $rolePermission = $this->createUserRole($roleAdministrator, $permission);
        $manager->persist($rolePermission);
        $count++;

        /** @var Permission|null $permission */
        $permission = $manager->getRepository(Permission::class)->findOneBy(['name' => 'profile.any.view']);
        if (empty($permission)) {
            throw new Exception('Permission profile.any.view doesn\'t exist');
        }
        $rolePermission = $this->createUserRole($roleAdministrator, $permission);
        $manager->persist($rolePermission);
        $count++;

        /** @var Permission|null $permission */
        $permission = $manager->getRepository(Permission::class)->findOneBy(['name' => 'role.manage']);
        if (empty($permission)) {
            throw new Exception('Permission role.manage doesn\'t exist');
        }
        $rolePermission = $this->createUserRole($roleAdministrator, $permission);
        $manager->persist($rolePermission);
        $count++;

        /** @var Permission|null $permission */
        $permission = $manager->getRepository(Permission::class)->findOneBy(['name' => 'user.manage']);
        if (empty($permission)) {
            throw new Exception('Permission user.manage doesn\'t exist');
        }
        $rolePermission = $this->createUserRole($roleAdministrator, $permission);
        $manager->persist($rolePermission);
        $count++;


        /** @var Role|null $roleGuest */
        $roleGuest = $manager->getRepository(Role::class)->findOneBy(['name' => 'Guest']);
        if (empty($roleGuest)) {
            throw new Exception('Guest role doesn\'t exist');
        }

        /** @var Permission|null $permission */
        $permission = $manager->getRepository(Permission::class)->findOneBy(['name' => 'profile.own.view']);
        if (empty($permission)) {
            throw new Exception('Permission profile.own.view doesn\'t exist');
        }
        $rolePermission = $this->createUserRole($roleGuest, $permission);
        $manager->persist($rolePermission);
        $count++;

        $manager->flush();
        $this->getCommand()->getProgressBar()->advance();
        /** @phpstan-ignore-next-line */
        $dbName = $manager->getConnection()->getDatabase();
        $this->getCommand()->getIo()->info('RolePermissionFixtures added ' . $count . ' items to MySql DB ' . $dbName);
    }

    /**
     * @param Role $roleGuest
     * @param Permission $permission
     *
     * @return RolePermission
     */
    private function createUserRole(Role $roleGuest, Permission $permission): RolePermission
    {
        $rolePermission = new RolePermission();
        $rolePermission->setRoleId($roleGuest->getId());
        $rolePermission->setPermissionId($permission->getId());

        $log = $this->createLog('Permission created with RoleId = ' . $roleGuest->getId()
            . ', PermissionId = ' . $permission->getId());
        $this->getCommand()->getDocumentManager()->persist($log);

        return $rolePermission;
    }
}
