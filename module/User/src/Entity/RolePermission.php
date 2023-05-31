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
 * This class represents a role permission
 * @ORM\Entity()
 * @ORM\Table(name="role_permission", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="role_id_permission_id", columns={"roleId", "permissionId"})
 * })
 * @package User\Entity
 */
class RolePermission
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=true)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var int $id
     */
    protected int $id;

    /**
     * @ORM\Column(name="permission_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @var int $permissionId
     */
    protected int $permissionId;

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
    public function setId(int $id): RolePermission
    {
        $this->id = $id;

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
    public function setRoleId(int $roleId): RolePermission
    {
        $this->roleId = $roleId;

        return $this;
    }

    /**
     * @return int
     */
    public function getPermissionId(): int
    {
        return $this->permissionId;
    }

    /**
     * @param int $permissionId
     *
     * @return $this
     */
    public function setPermissionId(int $permissionId): RolePermission
    {
        $this->permissionId = $permissionId;

        return $this;
    }
}
