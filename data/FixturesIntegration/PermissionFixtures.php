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

namespace FixturesIntegration;

use Carbon\Carbon;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Fixtures\AbstractFixtures;
use User\Entity\Permission;

/**
 * Auto-generated Permission Fixtures for Integration tests
 * @package FixturesIntegration
 */
class PermissionFixtures extends AbstractFixtures implements FixtureInterface
{
    /**
     * @param ObjectManager $manager
     *
     * @return void
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $permission = new Permission();
        $permission->setName('user.manage');
        $permission->setDescription('Manage users');
        $permission->setDateCreated(Carbon::parse('2023-01-01'));
        $manager->persist($permission);

        $permission = new Permission();
        $permission->setName('permission.manage');
        $permission->setDescription('Manage permissions');
        $permission->setDateCreated(Carbon::parse('2023-01-01'));
        $manager->persist($permission);

        $permission = new Permission();
        $permission->setName('role.manage');
        $permission->setDescription('Manage roles');
        $permission->setDateCreated(Carbon::parse('2023-01-01'));
        $manager->persist($permission);

        $permission = new Permission();
        $permission->setName('profile.any.view');
        $permission->setDescription("View anyone's profile");
        $permission->setDateCreated(Carbon::parse('2023-01-01'));
        $manager->persist($permission);

        $permission = new Permission();
        $permission->setName('profile.own.view');
        $permission->setDescription('View own profile');
        $permission->setDateCreated(Carbon::parse('2023-01-01'));
        $manager->persist($permission);

        $manager->flush();
    }
}
