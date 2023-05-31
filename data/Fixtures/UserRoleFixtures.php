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
use User\Entity\Role;
use User\Entity\User;
use User\Entity\UserRole;

/**
 * Auto-generated User Role Fixtures
 * @package Fixtures
 */
class UserRoleFixtures extends AbstractFixtures implements FixtureInterface
{
    /** @var int $count */
    protected int $count = 0;

    /**
     * UserRoleFixtures construct
     * @param AbstractCommand $command
     * @param int $count
     *
     * @throws Exception
     */
    public function __construct(AbstractCommand $command, int $count = 0)
    {
        parent::__construct([self::INIT_COUNT_USERS]);
        $this->command = $command;
        $this->count = $count;
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
        $userRole = $this->createUserRole($role, $user);
        $manager->persist($userRole);

        /** @var Role|null $roleGuest */
        $roleGuest = $manager->getRepository(Role::class)->findOneBy(['name' => 'Guest']);
        if (empty($roleGuest)) {
            throw new Exception('Guest role doesn\'t exist');
        }
        $countUsers = ($this->count > 0) ? $this->count : $this->getCountUsers();
        for ($i = 1; $i <= $countUsers; $i++) {
            /** @var User|null $user */
            $user = $manager->getRepository(User::class)->findOneBy(['email' => "guest$i@example.com"]);
            if (empty($user)) {
                throw new Exception("User with email guest$i@example.com doesn't exist");
            }
            $userRole = $this->createUserRole($roleGuest, $user);
            $manager->persist($userRole);
        }

        $manager->flush();
        $this->getCommand()->getProgressBar()->advance();
        /** @phpstan-ignore-next-line */
        $dbName = $manager->getConnection()->getDatabase();
        $this->getCommand()->getIo()->info('UserRoleFixtures added ' . $countUsers . ' items to MySql DB ' . $dbName);
    }

    /**
     * @param Role $role
     * @param User $user
     *
     * @return UserRole
     */
    private function createUserRole(Role $role, User $user): UserRole
    {
        $userRole = new UserRole();
        $userRole->setRoleId($role->getId());
        $userRole->setUserId($user->getId());
        $userRole->setUserArchivedId(0);

        $log = $this->createLog('Permission created with RoleId = ' . $role->getId()
            . ', UserId = ' . $user->getId());
        $this->getCommand()->getDocumentManager()->persist($log);

        return $userRole;
    }
}
