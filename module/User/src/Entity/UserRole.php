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

namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a user role
 * @ORM\Entity()
 * @ORM\Table(name="user_role", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="user_id_user_archived_id_role_id", columns={"userId", "userArchivedId", "roleId"})
 * })
 * @package User\Entity
 */
class UserRole
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=true)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var int $id
     */
    protected int $id;

    /**
     * @ORM\Column(name="user_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @var int $userId
     */
    protected int $userId;

    /**
     * @ORM\Column(name="user_archived_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @var int $userArchivedId
     */
    protected int $userArchivedId;

    /**
     * @ORM\Column(name="role_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @var int $roleId
     */
    protected int $roleId;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId(int $id): UserRole
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     *
     * @return $this
     */
    public function setUserId(int $userId): UserRole
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return int
     */
    public function getRoleId(): int
    {
        return $this->roleId;
    }

    /**
     * @param int $roleId
     *
     * @return $this
     */
    public function setRoleId(int $roleId): UserRole
    {
        $this->roleId = $roleId;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserArchivedId(): int
    {
        return $this->userArchivedId;
    }

    /**
     * @param int $userArchivedId
     *
     * @return $this
     */
    public function setUserArchivedId(int $userArchivedId): UserRole
    {
        $this->userArchivedId = $userArchivedId;

        return $this;
    }
}
