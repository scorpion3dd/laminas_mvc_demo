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

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;

/**
 * This class represents a role
 * @ORM\Entity(repositoryClass="\User\Repository\RoleRepository")
 * @ORM\Table(name="role")
 * @package User\Entity
 */
class Role
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=true)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var int|null $id
     */
    protected ?int $id = 0;

    /**
     * @ORM\Column(name="name", type="string", length=128, precision=0, scale=0, nullable=false, unique=true)
     * @var string $name
     */
    protected string $name;

    /**
     * @ORM\Column(name="description", type="string", length=1024, precision=0, scale=0, nullable=false, unique=false)
     * @var string $description
     */
    protected string $description;

    /**
     * @ORM\Column(name="date_created", type="datetime", precision=0, scale=0, nullable=false, unique=false)
     * @var DateTime $dateCreated
     */
    protected DateTime $dateCreated;

    /**
     * @ORM\ManyToMany(targetEntity="User\Entity\Role", inversedBy="childRoles")
     * @ORM\JoinTable(name="role_hierarchy",
     *      joinColumns={@ORM\JoinColumn(name="child_role_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="parent_role_id", referencedColumnName="id")}
     *      )
     * @var ArrayCollection|PersistentCollection $parentRoles
     */
    private ArrayCollection|PersistentCollection $parentRoles;

    /**
     * @ORM\ManyToMany(targetEntity="User\Entity\Role", mappedBy="parentRoles")
     * @ORM\JoinTable(name="role_hierarchy",
     *      joinColumns={@ORM\JoinColumn(name="parent_role_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="child_role_id", referencedColumnName="id")}
     *      )
     * @var ArrayCollection|PersistentCollection $childRoles
     */
    protected ArrayCollection|PersistentCollection $childRoles;

    /**
     * @ORM\ManyToMany(targetEntity="User\Entity\Permission", inversedBy="roles")
     * @ORM\JoinTable(name="role_permission",
     *      joinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="permission_id", referencedColumnName="id")}
     *      )
     * @var ArrayCollection|PersistentCollection $permissions
     */
    private ArrayCollection|PersistentCollection $permissions;

    /**
     * Role constructor
     */
    public function __construct()
    {
        $this->parentRoles = new ArrayCollection();
        $this->childRoles = new ArrayCollection();
        $this->permissions = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     *
     * @return $this
     */
    public function setId(?int $id): Role
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): Role
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription(string $description): Role
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateCreated(): DateTime
    {
        return $this->dateCreated;
    }

    /**
     * @param DateTime $dateCreated
     *
     * @return $this
     */
    public function setDateCreated(DateTime $dateCreated): Role
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getParentRoles(): ArrayCollection|PersistentCollection
    {
        return $this->parentRoles;
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getChildRoles(): ArrayCollection|PersistentCollection
    {
        return $this->childRoles;
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getPermissions(): ArrayCollection|PersistentCollection
    {
        return $this->permissions;
    }

    /**
     * @param Permission $permission
     *
     * @return void
     */
    public function setPermissions(Permission $permission): void
    {
        $this->permissions->add($permission);
    }

    /**
     * @param Role $role
     *
     * @return bool
     */
    public function addParent(Role $role): bool
    {
        if ($this->getId() == $role->getId()) {
            return false;
        }
        if (! $this->hasParent($role)) {
            $this->parentRoles->add($role);
            $role->getChildRoles()->add($this);

            return true;
        }

        return false;
    }

    /**
     * @return void
     */
    public function clearParentRoles(): void
    {
        $this->parentRoles = new ArrayCollection();
    }

    /**
     * @param Role $role
     *
     * @return void
     */
    public function setParentRole(Role $role): void
    {
        $this->parentRoles->add($role);
    }

    /**
     * @param Role $role
     *
     * @return bool
     */
    public function hasParent(Role $role): bool
    {
        return $this->getParentRoles()->contains($role);
    }
}
