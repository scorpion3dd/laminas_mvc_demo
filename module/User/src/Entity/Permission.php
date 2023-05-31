<?php
/**
 * This file is part of the Simple Web Demo Free Lottery Management Application.
 *
 * This project is no longer maintained.
 * The project is written in Zend Framework 3 Release.
 *
 * @link https://github.com/scorpion3dd
 * @author Denis Puzik <scorpion3dd@gmail.com>
 * @copyright Copyright (c) 2020-2021 scorpion3dd
 */

declare(strict_types=1);

namespace User\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;

/**
 * This class represents a permission
 * @ORM\Entity()
 * @ORM\Table(name="permission")
 * @package User\Entity
 */
class Permission
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=true)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var int $id
     */
    protected int $id;

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
     * @ORM\ManyToMany(targetEntity="User\Entity\Role", mappedBy="permissions")
     * @ORM\JoinTable(name="role_permission",
     *      joinColumns={@ORM\JoinColumn(name="permission_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     *      )
     * @var ArrayCollection|PersistentCollection $roles
     */
    private ArrayCollection|PersistentCollection $roles;

    /**
     * Permission constructor
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

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
    public function setId(int $id): Permission
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
    public function setName(string $name): Permission
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
    public function setDescription(string $description): Permission
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
    public function setDateCreated(DateTime $dateCreated): Permission
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }
}
