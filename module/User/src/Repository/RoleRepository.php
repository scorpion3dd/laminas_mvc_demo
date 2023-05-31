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

/**
 * This is the custom repository class for Role entity
 * @package User\Repository
 */
class RoleRepository extends EntityRepository
{
    public const DEFAULT_ROLES = [
        'Administrator' => [
            'description' => 'A person who manages users, roles, etc.',
            'parent' => null,
            'permissions' => [
                'user.manage',
                'role.manage',
                'permission.manage',
                'profile.any.view',
            ],
        ],
        'Guest' => [
            'description' => 'A person who can log in and view own profile.',
            'parent' => null,
            'permissions' => [
                'profile.own.view',
            ],
        ],
    ];

    /**
     * @return array
     */
    public function getAllDefaultRoles(): array
    {
        return self::DEFAULT_ROLES;
    }
}
