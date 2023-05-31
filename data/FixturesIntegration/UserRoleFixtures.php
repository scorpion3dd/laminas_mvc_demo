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

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Fixtures\AbstractFixtures;
use User\Entity\Role;
use User\Entity\User;
use User\Entity\UserRole;

/**
 * Auto-generated User Role Fixtures for Integration tests
 * @package FixturesIntegration
 */
class UserRoleFixtures extends AbstractFixtures implements FixtureInterface
{
    /**
     * UserRoleFixtures construct
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct([self::INIT_COUNT_USERS_INTEGRATION]);
    }

    /**
     * @param ObjectManager $manager
     *
     * @return void
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        /** @var Role|null $role */
        $role = $manager->getRepository(Role::class)->findOneBy(['name' => 'Administrator']);
        if (empty($role)) {
            throw new Exception('Administrator role doesn\'t exist');
        }
        /** @var User|null $user */
        $user = $manager->getRepository(User::class)->findOneBy(['email' => User::EMAIL_ADMIN]);
        if (empty($user)) {
            throw new Exception('User with email doesn\'t exist');
        }
        $userRole = new UserRole();
        $userRole->setRoleId($role->getId());
        $userRole->setUserId($user->getId());
        $userRole->setUserArchivedId(0);
        $manager->persist($userRole);

        /** @var Role|null $roleGuest */
        $roleGuest = $manager->getRepository(Role::class)->findOneBy(['name' => 'Guest']);
        if (empty($roleGuest)) {
            throw new Exception('Administrator role doesn\'t exist');
        }
        for ($i = 1; $i <= $this->getCountUsers(); $i++) {
            /** @var User|null $user */
            $user = $manager->getRepository(User::class)->findOneBy(['email' => "guest$i@example.com"]);
            if (empty($user)) {
                throw new Exception('User with email doesn\'t exist');
            }
            $userRole = new UserRole();
            $userRole->setRoleId($roleGuest->getId());
            $userRole->setUserId($user->getId());
            $userRole->setUserArchivedId(0);
            $manager->persist($userRole);
        }

        $manager->flush();
    }
}
