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
 * This class represents a registered user
 * @ORM\Entity(repositoryClass="\User\Repository\UserRepository")
 * @ORM\Table(name="user")
 * @package User\Entity
 */
class User
{
    public const STATUS_ACTIVE_ID = 1;
    public const STATUS_ACTIVE = 'Active';
    public const STATUS_DISACTIVE_ID = 2;
    public const STATUS_DISACTIVE = 'Disactive';

    public const GENDER_MALE_ID = 1;
    public const GENDER_MALE = 'Male';
    public const GENDER_FEMALE_ID = 2;
    public const GENDER_FEMALE = 'Female';

    public const ACCESS_YES_ID = 1;
    public const ACCESS_YES = 'Yes';
    public const ACCESS_NO_ID = 2;
    public const ACCESS_NO = 'No';

    public const EMAIL_ADMIN = 'admin@example.com';
    public const FULL_NAME_ADMIN = 'Admin';
    public const PASSWORD_ADMIN = 'admin123';
    public const PASSWORD_GUEST = 'guest123';

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=true)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var int $id
     */
    protected int $id;

    /**
     * @ORM\Column(name="email", type="string", length=128, precision=0, scale=0, nullable=false, unique=true)
     * @var string $email
     */
    protected string $email;

    /**
     * @ORM\Column(name="full_name", type="string", length=256, precision=0, scale=0, nullable=false, unique=false)
     * @var string $fullName
     */
    protected string $fullName;

    /**
     * @ORM\Column(name="description", type="string", length=1024, precision=0, scale=0, nullable=true, unique=false)
     * @var string|null $description
     */
    protected ?string $description;

    /**
     * @ORM\Column(name="password", type="string", length=128, precision=0, scale=0, nullable=true, unique=false)
     * @var string $password
     */
    protected string $password;

    /**
     * @ORM\Column(name="status", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @var int $status
     */
    protected int $status;

    /**
     * @ORM\Column(name="access", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @var int $access
     */
    protected int $access;

    /**
     * @ORM\Column(name="gender", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @var int $gender
     */
    protected int $gender;

    /**
     * @ORM\Column(name="date_birthday", type="datetime", precision=0, scale=0, nullable=false, unique=false)
     * @var DateTime $dateBirthday
     */
    protected DateTime $dateBirthday;

    /**
     * @ORM\Column(name="date_created", type="datetime", precision=0, scale=0, nullable=false, unique=false)
     * @var DateTime $dateCreated
     */
    protected DateTime $dateCreated;

    /**
     * @ORM\Column(name="date_updated", type="datetime", precision=0, scale=0, nullable=false, unique=false)
     * @var DateTime $dateUpdated
     */
    protected DateTime $dateUpdated;

    /**
     * @ORM\Column(name="pwd_reset_token", type="string", length=128, precision=0, scale=0, nullable=true, unique=false)
     * @var string|null $passwordResetToken
     */
    protected ?string $passwordResetToken;

    /**
     * @ORM\Column(name="pwd_reset_token_creation_date", type="datetime", precision=0, scale=0, nullable=true, unique=false)
     * @var DateTime|null $passwordResetTokenCreationDate
     */
    protected ?DateTime $passwordResetTokenCreationDate;

    /**
     * @ORM\ManyToMany(targetEntity="User\Entity\Role")
     * @ORM\JoinTable(name="user_role",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     *      )
     * @var ArrayCollection|PersistentCollection $roles
     */
    private ArrayCollection|PersistentCollection $roles;

    /**
     * User constructor
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
    public function setId(int $id): User
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->fullName;
    }

    /**
     * @param string $fullName
     *
     * @return $this
     */
    public function setFullName(string $fullName): User
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return string[]
     */
    public static function getStatusList(): array
    {
        return [
            self::STATUS_ACTIVE_ID => self::STATUS_ACTIVE,
            self::STATUS_DISACTIVE_ID => self::STATUS_DISACTIVE
        ];
    }

    /**
     * @return string
     */
    public function getStatusAsString(): string
    {
        $list = self::getStatusList();
        if (isset($list[$this->status])) {
            return $list[$this->status];
        }

        return 'Unknown';
    }

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus(int $status): User
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;

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
    public function setDateCreated(DateTime $dateCreated): User
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPasswordResetToken(): ?string
    {
        return $this->passwordResetToken;
    }

    /**
     * @param string|null $token
     *
     * @return $this
     */
    public function setPasswordResetToken(?string $token): User
    {
        $this->passwordResetToken = $token;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getPasswordResetTokenCreationDate(): DateTime
    {
        return $this->passwordResetTokenCreationDate;
    }

    /**
     * @param DateTime|null $date
     *
     * @return $this
     */
    public function setPasswordResetTokenCreationDate(?DateTime $date): User
    {
        $this->passwordResetTokenCreationDate = $date;

        return $this;
    }

    /**
     * @return ArrayCollection|PersistentCollection
     */
    public function getRoles(): ArrayCollection|PersistentCollection
    {
        return $this->roles;
    }

    /**
     * @return string
     */
    public function getRolesAsString(): string
    {
        $roleList = '';
        $count = count($this->roles);
        $i = 0;
        foreach ($this->roles as $role) {
            $roleList .= $role->getName();
            if ($i < $count - 1) {
                $roleList .= ', ';
            }
            $i++;
        }

        return $roleList;
    }

    /**
     * Assigns a role to user
     * @param Role $role
     *
     * @return void
     */
    public function addRole(Role $role): void
    {
        $this->roles->add($role);
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return $this
     */
    public function setDescription(?string $description): User
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateUpdated(): DateTime
    {
        return $this->dateUpdated;
    }

    /**
     * @param DateTime $dateUpdated
     *
     * @return $this
     */
    public function setDateUpdated(DateTime $dateUpdated): User
    {
        $this->dateUpdated = $dateUpdated;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateBirthday(): DateTime
    {
        return $this->dateBirthday;
    }

    /**
     * @param DateTime $dateBirthday
     *
     * @return $this
     */
    public function setDateBirthday(DateTime $dateBirthday): User
    {
        $this->dateBirthday = $dateBirthday;

        return $this;
    }

    /**
     * @return int
     */
    public function getAccess(): int
    {
        return $this->access;
    }

    /**
     * @param int $access
     *
     * @return $this
     */
    public function setAccess(int $access): User
    {
        $this->access = $access;

        return $this;
    }

    /**
     * @return string[]
     */
    public static function getAccessList(): array
    {
        return [
            self::ACCESS_YES_ID => self::ACCESS_YES,
            self::ACCESS_NO_ID => self::ACCESS_NO
        ];
    }

    /**
     * @return string
     */
    public function getAccessAsString(): string
    {
        $list = self::getAccessList();
        if (isset($list[$this->access])) {
            return $list[$this->access];
        }

        return 'Unknown';
    }

    /**
     * @return int
     */
    public function getGender(): int
    {
        return $this->gender;
    }

    /**
     * @param int $gender
     *
     * @return $this
     */
    public function setGender(int $gender): User
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return string[]
     */
    public static function getGenderList(): array
    {
        return [
            self::GENDER_MALE_ID => self::GENDER_MALE,
            self::GENDER_FEMALE_ID => self::GENDER_FEMALE
        ];
    }

    /**
     * @return string
     */
    public function getGenderAsString(): string
    {
        $list = self::getGenderList();
        if (isset($list[$this->gender])) {
            return $list[$this->gender];
        }

        return 'Unknown';
    }
}
