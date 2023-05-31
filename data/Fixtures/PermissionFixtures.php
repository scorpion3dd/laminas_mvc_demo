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
use Carbon\Carbon;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use User\Entity\Permission;

/**
 * Auto-generated Permission Fixtures
 * @package Fixtures
 */
class PermissionFixtures extends AbstractFixtures implements FixtureInterface
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
        $permission = $this->createPermission('user.manage', 'Manage users');
        $manager->persist($permission);
        $count++;

        $permission = $this->createPermission('permission.manage', 'Manage permissions');
        $manager->persist($permission);
        $count++;

        $permission = $this->createPermission('role.manage', 'Manage roles');
        $manager->persist($permission);
        $count++;

        $permission = $this->createPermission('profile.any.view', "View anyone's profile");
        $manager->persist($permission);
        $count++;

        $permission = $this->createPermission('profile.own.view', 'View own profile');
        $manager->persist($permission);
        $count++;

        $manager->flush();
        $this->getCommand()->getProgressBar()->advance();
        /** @phpstan-ignore-next-line */
        $dbName = $manager->getConnection()->getDatabase();
        $this->getCommand()->getIo()->info('PermissionFixtures added ' . $count . ' items to MySql DB ' . $dbName);
    }

    /**
     * @param string $name
     * @param string $description
     *
     * @return Permission
     */
    private function createPermission(string $name, string $description): Permission
    {
        $permission = new Permission();
        $permission->setName($name);
        $permission->setDescription($description);
        $permission->setDateCreated(Carbon::now());

        $log = $this->createLog('Permission created with name = ' . $name);
        $this->getCommand()->getDocumentManager()->persist($log);

        return $permission;
    }
}
