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

use Carbon\Carbon;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Fixtures\AbstractFixtures;
use User\Entity\User;
use User\Service\UserManager;
use Laminas\Crypt\Password\Bcrypt;

/**
 * Auto-generated User Fixtures for Integration tests
 * @package FixturesIntegration
 */
class UserFixtures extends AbstractFixtures implements FixtureInterface
{
    /**
     * UserFixtures construct
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
        $user = new User();
        $user->setEmail(User::EMAIL_ADMIN);
        $user->setFullName(User::FULL_NAME_ADMIN);
        $bcrypt = new Bcrypt();
        $passwordHash = $bcrypt->create(User::PASSWORD_ADMIN);
        $user->setPassword($passwordHash);
        $user->setStatus(User::STATUS_ACTIVE_ID);
        $user->setAccess(User::ACCESS_NO_ID);
        $user->setGender(User::GENDER_MALE_ID);
        $user->setDateBirthday(Carbon::parse('1980-11-25'));
        $user->setDateCreated(Carbon::parse('2023-01-01'));
        $user->setDateUpdated(Carbon::parse('2023-01-01'));
        $manager->persist($user);
        $manager->flush();

        $countUsers = $this->getCountUsers();
        for ($i = 1; $i <= $countUsers; $i++) {
            $user = new User();
            $user->setEmail("guest$i@example.com");
            $genderId = ($i % 2 === 0) ? User::GENDER_MALE_ID : User::GENDER_FEMALE_ID;
            $user->setFullName('Danny Fay');
            $user->setDescription('Temporibus quis et ad perspiciatis. Hic accusamus id porro aut expedita '
                . 'iusto ut. Ea consequatur saepe quis sed id veritatis a. Ratione eos expedita eum nihil dolorum '
                . 'maiores. Iusto commodi quibusdam quo sit praesentium ut sunt consequatur. Recusandae aliquid '
                . 'saepe itaque quas dolore. Et perspiciatis commodi commodi reiciendis voluptates. Possimus animi '
                . 'adipisci voluptatem suscipit similique. Voluptates saepe sit rerum architecto repellendus. '
                . 'Beatae ut nihil hic aliquam quisquam similique voluptatem. Amet autem magnam aut. '
                . 'Asperiores voluptatem enim nulla et consectetur aliquam. Quibusdam autem eveniet iste amet '
                . 'alias quis suscipit rerum. Incidunt molestiae esse ut eligendi. Qui inventore blanditiis nesciunt quia.');
            $bcrypt = new Bcrypt();
            $passwordHash = $bcrypt->create(User::PASSWORD_GUEST);
            $user->setPassword($passwordHash);
            $statusId = ($i % 2 === 0) ? User::STATUS_ACTIVE_ID : User::STATUS_DISACTIVE_ID;
            $user->setStatus($statusId);
            $accessId = ($i % 2 === 0) ? User::ACCESS_YES_ID : User::ACCESS_NO_ID;
            $user->setAccess($accessId);
            $user->setGender($genderId);
            $user->setDateBirthday(Carbon::parse('1985-10-12'));
            $user->setDateCreated(Carbon::parse('2023-01-01'));
            $user->setDateUpdated(Carbon::parse('2023-01-01'));
            $manager->persist($user);
        }

        $manager->flush();
        echo PHP_EOL
            . 'UserFixtures added ' . $countUsers . ' items for integration tests to MySql DB'
            . PHP_EOL;
    }
}
