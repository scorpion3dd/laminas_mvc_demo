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
use User\Entity\Role;

/**
 * Auto-generated Role Fixtures
 * @package Fixtures
 */
class RoleFixtures extends AbstractFixtures implements FixtureInterface
{
    public const REDIS_ROLE = 'role:';
    public const REDIS_ROLE_INTEGRATION = 'role:integration:';
    public const REDIS_SETS_ROLES = 'roles';
    public const REDIS_SETS_ROLES_INTEGRATION = 'roles:integration';
    public const REDIS_ROLE_SET = 'role:set';
    public const REDIS_ROLE_SET_INTEGRATION = 'role:integration:set';
    public const REDIS_SETS_ROLES_TTL = 600;

    /**
     * RoleFixtures construct
     * @param AbstractCommand $command
     *
     * @throws Exception
     */
    public function __construct(AbstractCommand $command)
    {
        parent::__construct([self::INIT_REDIS]);
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
        $roles = [];
        $roleAdmin = $this->createRole('Administrator', 'A person who manages users, roles, etc.');
        $roles[] = $roleAdmin;
        $manager->persist($roleAdmin);

        $roleGuest = $this->createRole('Guest', 'A person who can log in and view own profile.');
        $roles[] = $roleGuest;
        $manager->persist($roleGuest);

        $roleDemo = $this->createRole('Demo', 'A person for demonstration.');
        $roles[] = $roleDemo;
        $manager->persist($roleDemo);

        $manager->flush();

        $this->createRoleRedis($roles);
        $this->getCommand()->getProgressBar()->advance();
        /** @phpstan-ignore-next-line */
        $dbName = $manager->getConnection()->getDatabase();
        $this->getCommand()->getIo()->info('RoleFixtures added ' . count($roles) . ' items to MySql DB ' . $dbName);
    }

    /**
     * @param string $name
     * @param string $description
     *
     * @return Role
     */
    private function createRole(string $name, string $description): Role
    {
        $role = new Role();
        $role->setName($name);
        $role->setDescription($description);
        $role->setDateCreated(Carbon::now());

        $log = $this->createLog('Role created with name = ' . $name);
        $this->getCommand()->getDocumentManager()->persist($log);

        return $role;
    }

    /**
     * @param array $roles
     *
     * @return void
     */
    private function createRoleRedis(array $roles): void
    {
        $countRolesRedis = (int)$this->redis->lLen(self::REDIS_SETS_ROLES);
        if (! empty($countRolesRedis) && $countRolesRedis > 0) {
            $this->redis->del(self::REDIS_SETS_ROLES);
        }
        $countRoles = 0;
        foreach ($roles as $role) {
            $roleSerialize = serialize($role);
            $this->redis->rPush(self::REDIS_SETS_ROLES, $roleSerialize);
            $this->redis->set(
                self::REDIS_ROLE . $role->getId(),
                serialize($role),
                ['EX' => self::REDIS_SETS_ROLES_TTL]
            );
            $countRoles++;
        }
        $this->redis->expire(self::REDIS_SETS_ROLES, self::REDIS_SETS_ROLES_TTL);

        $this->getCommand()->getIo()->info('RoleFixtures added ' . $countRoles . ' items to Redis DB in set ' . self::REDIS_SETS_ROLES);
    }
}
