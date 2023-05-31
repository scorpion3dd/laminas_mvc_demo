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
use User\Entity\User;
use Laminas\Crypt\Password\Bcrypt;

/**
 * Auto-generated User Fixtures
 * @package Fixtures
 */
class UserFixtures extends AbstractFixtures implements FixtureInterface
{
    /** @var int $count */
    protected int $count = 0;

    /**
     * UserFixtures construct
     * @param AbstractCommand $command
     * @param int $count
     *
     * @throws Exception
     */
    public function __construct(AbstractCommand $command, int $count = 0)
    {
        parent::__construct([self::INIT_COUNT_USERS, self::INIT_FAKER]);
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
        $user = new User();
        $user->setEmail(User::EMAIL_ADMIN);
        $user->setFullName(User::FULL_NAME_ADMIN);
        $bcrypt = new Bcrypt();
        $passwordHash = $bcrypt->create(User::PASSWORD_ADMIN);
        $user->setPassword($passwordHash);
        $user->setStatus(User::STATUS_ACTIVE_ID);
        $user->setAccess(User::ACCESS_NO_ID);
        $user->setGender(User::GENDER_MALE_ID);
        $user->setDateBirthday($this->faker->dateTimeBetween('-50 years', '-20 years'));
        $user->setDateCreated($this->faker->dateTimeBetween('-5 years', 'now'));
        $user->setDateUpdated($this->faker->dateTimeBetween('-2 years', 'now'));
        $manager->persist($user);
        $log = $this->createLog('User created with Email = ' . $user->getEmail());
        $this->getCommand()->getDocumentManager()->persist($log);

        $countUsers = ($this->count > 0) ? $this->count : $this->getCountUsers();
        for ($i = 1; $i <= $countUsers; $i++) {
            $user = new User();
            $user->setEmail("guest$i@example.com");
            $genderId = random_int(User::GENDER_MALE_ID, User::GENDER_FEMALE_ID);
            $gender = $genderId == User::GENDER_MALE_ID ? User::GENDER_MALE : User::GENDER_FEMALE;
            $user->setFullName($this->faker->name($gender));
            $user->setDescription($this->faker->text(1024));
            $bcrypt = new Bcrypt();
            $passwordHash = $bcrypt->create(User::PASSWORD_GUEST);
            $user->setPassword($passwordHash);
            $statusId = random_int(User::STATUS_ACTIVE_ID, User::STATUS_DISACTIVE_ID);
            $user->setStatus($statusId);
            $accessId = random_int(User::ACCESS_YES_ID, User::ACCESS_NO_ID);
            $user->setAccess($accessId);
            $user->setGender($genderId);
            $user->setDateBirthday($this->faker->dateTimeBetween('-50 years', '-20 years'));
            $user->setDateCreated($this->faker->dateTimeBetween('-15 years', 'now'));
            $user->setDateUpdated($this->faker->dateTimeBetween('-10 years', 'now'));
            $manager->persist($user);

            $log = $this->createLog('User created with Email = ' . $user->getEmail());
            $this->getCommand()->getDocumentManager()->persist($log);
        }

        $manager->flush();
        $this->getCommand()->getProgressBar()->advance();
        /** @phpstan-ignore-next-line */
        $dbName = $manager->getConnection()->getDatabase();
        $this->getCommand()->getIo()->info('UserFixtures added ' . $countUsers . ' items to MySql DB ' . $dbName);
    }
}
