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

namespace User\Service;

use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Exception;
use Faker\Factory;
use Faker\Generator;
use User\Entity\User;
use User\Entity\Role;
use User\Kafka\ProducerKafka;
use User\Repository\UserRepository;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Log\Logger;
use Laminas\Math\Rand;
use Laminas\Mail;
use Laminas\Mail\Transport\Smtp as SmtpTransport;
use Laminas\Mail\Transport\SmtpOptions;
use Laminas\Mime\Message as MimeMessage;
use Laminas\Mime\Part as MimePart;
use Laminas\Paginator\Paginator;
use Laminas\View\Renderer\PhpRenderer;

/**
 * This service is responsible for adding/editing users
 * and changing user password
 * @package User\Service
 */
class UserManager
{
    /** @var EntityManager $entityManager */
    private EntityManager $entityManager;

    /** @var RoleManager $roleManager */
    private RoleManager $roleManager;

    /** @var PermissionManager $permissionManager */
    private PermissionManager $permissionManager;

    /** @var PhpRenderer $viewRenderer */
    private PhpRenderer $viewRenderer;

    /** @var array $config */
    private array $config;

    /** @var Generator $faker */
    protected Generator $faker;

    /** @var Logger $logger */
    protected Logger $logger;

    /** @var ProducerKafka $producerKafka */
    private ProducerKafka $producerKafka;

    /** @var SmtpTransport $transport */
    private SmtpTransport $transport;

    /**
     * UserManager constructor
     * @param Logger $logger
     * @param EntityManager $entityManager
     * @param RoleManager $roleManager
     * @param PermissionManager $permissionManager
     * @param PhpRenderer $viewRenderer
     * @param array $config
     */
    public function __construct(
        Logger $logger,
        EntityManager $entityManager,
        RoleManager $roleManager,
        PermissionManager $permissionManager,
        PhpRenderer $viewRenderer,
        array $config
    ) {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->roleManager = $roleManager;
        $this->permissionManager = $permissionManager;
        $this->viewRenderer = $viewRenderer;
        $this->config = $config;
        $this->faker = Factory::create();
    }

    /**
     * @param User|null $currentUser
     * @param array $data
     *
     * @return User
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function addUser(?User $currentUser, array $data): User
    {
        if ($this->checkUserExists($data['email'])) {
            $message = "User with email address " . $data['email'] . " already exists";
            $this->logger->log(Logger::ERR, $message);
            throw new Exception($message);
        }
        $user = new User();
        $user->setEmail($data['email']);
        $user->setFullName($data['full_name']);
        $user->setDescription($data['description']);
        $bcrypt = new Bcrypt();
        $passwordHash = $bcrypt->create($data['password']);
        $user->setPassword($passwordHash);
        $user->setGender($data['gender']);
        $user->setStatus($data['status']);
        $user->setAccess($data['access']);
        $user->setDateBirthday(Carbon::parse($data['date_birthday']));
        $user->setDateCreated(Carbon::now());
        $user->setDateUpdated(Carbon::now());
        $this->assignRoles($user, $data['roles']);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $message = 'Your status set to - ';
        if ($data['status'] == User::STATUS_ACTIVE_ID) {
            $message .= User::STATUS_ACTIVE;
        }
        if ($data['status'] == User::STATUS_DISACTIVE_ID) {
            $message .= User::STATUS_DISACTIVE;
        }
        $message .= ' <br>Your access set to - ';
        if ($data['access'] == User::ACCESS_YES_ID) {
            $message .= User::ACCESS_YES;
        }
        if ($data['access'] == User::ACCESS_NO_ID) {
            $message .= User::ACCESS_NO;
        }
        $this->getProducerKafka()->send($message, 'addUser', $currentUser, $user);

        return $user;
    }

    /**
     * @param User $user
     * @param User|null $currentUser
     * @param array $data
     *
     * @return bool
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function updateUser(User $user, ?User $currentUser, array $data): bool
    {
        if ($user->getEmail() != $data['email'] && $this->checkUserExists($data['email'])) {
            $message = "Another user with email address " . $data['email'] . " already exists";
            $this->logger->log(Logger::ERR, $message);
            throw new Exception($message);
        }
        $user->setEmail($data['email']);
        $user->setFullName($data['full_name']);
        $user->setDescription($data['description']);
        $user->setDateBirthday(Carbon::parse($data['date_birthday']));
        $user->setGender($data['gender']);
        if ($user->getStatus() != $data['status']) {
            $message = 'Your status changed to - ';
            if ($data['status'] == User::STATUS_ACTIVE_ID) {
                $message .= User::STATUS_ACTIVE;
            }
            if ($data['status'] == User::STATUS_DISACTIVE_ID) {
                $message .= User::STATUS_DISACTIVE;
            }
            $this->getProducerKafka()->send($message, 'updateUser', $currentUser, $user);
        }
        $user->setStatus($data['status']);
        if ($user->getAccess() != $data['access']) {
            $message = 'Your access to recurse changed to - ';
            if ($data['access'] == User::ACCESS_YES_ID) {
                $message .= User::ACCESS_YES;
            }
            if ($data['access'] == User::ACCESS_NO_ID) {
                $message .= User::ACCESS_NO;
            }
            $this->getProducerKafka()->send($message, 'updateUser', $currentUser, $user);
        }
        $user->setAccess($data['access']);
        $this->assignRoles($user, $data['roles']);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param User $user
     * @param array $roleIds
     *
     * @return void
     * @throws Exception
     */
    private function assignRoles(User $user, array $roleIds): void
    {
        // Remove old user role(s)
        $user->getRoles()->clear();
        // Assign new role(s)
        foreach ($roleIds as $roleId) {
            /** @var Role|null $role */
            $role = $this->entityManager->getRepository(Role::class)->find($roleId);
            if (empty($role)) {
                $message = 'Not found role by ID';
                $this->logger->log(Logger::ERR, $message);
                throw new Exception($message);
            }
            $user->addRole($role);
        }
    }

    /**
     * This method checks if at least one user presents, and if not, creates
     * 'Admin' user with email 'admin@example.com' and password 'admin'
     *
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createAdminUserIfNotExists(): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([]);
        if (empty($user)) {
            $this->permissionManager->createDefaultPermissionsIfNotExist();
            $this->roleManager->createDefaultRolesIfNotExist();
            $user = new User();
            $user->setEmail(User::EMAIL_ADMIN);
            $user->setFullName(User::FULL_NAME_ADMIN);
            $bcrypt = new Bcrypt();
            $passwordHash = $bcrypt->create(User::PASSWORD_ADMIN);
            $user->setPassword($passwordHash);
            $user->setStatus(User::STATUS_ACTIVE_ID);
            $user->setGender(User::GENDER_MALE_ID);
            $user->setAccess(User::ACCESS_NO_ID);
            $user->setDateBirthday($this->faker->dateTimeBetween('-50 years', '-20 years'));
            $user->setDateCreated(Carbon::now());
            $user->setDateUpdated(Carbon::now());
            // Assign user Administrator role
            /** @phpstan-ignore-next-line */
            $adminRole = $this->entityManager->getRepository(Role::class)->findOneByName('Administrator');
            if (empty($adminRole)) {
                $message = 'Administrator role doesn\'t exist';
                $this->logger->log(Logger::ERR, $message);
                throw new Exception($message);
            }
            $user->getRoles()->add($adminRole);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }

    /**
     * Checks whether an active user with given email address already exists in the database
     * @param string $email
     *
     * @return bool
     */
    private function checkUserExists(string $email): bool
    {
        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        return $user !== null;
    }

    /**
     * Checks that the given password is correct
     * @param User $user
     * @param string $password
     *
     * @return bool
     */
    private function validatePassword(User $user, string $password): bool
    {
        $bcrypt = new Bcrypt();
        $passwordHash = $user->getPassword();

        return $bcrypt->verify($password, $passwordHash);
    }

    /**
     * Generates a password reset token for the user. This token is then stored in database and
     * sent to the user's E-mail address. When the user clicks the link in E-mail message, he is
     * directed to the Set Password page
     * @param User $user
     *
     * @return void
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function generatePasswordResetToken(User $user): void
    {
        if ($user->getStatus() != User::STATUS_ACTIVE_ID) {
            $message = 'Cannot generate password reset token for inactive user ' . $user->getEmail();
            $this->logger->log(Logger::ERR, $message);
            throw new Exception($message);
        }
        // Generate a token
        $token = self::getRandomToken();
        // Encrypt the token before storing it in DB
        $bcrypt = new Bcrypt();
        $tokenHash = $bcrypt->create($token);
        $user->setPasswordResetToken($tokenHash);
        $user->setPasswordResetTokenCreationDate(Carbon::now());
        $this->entityManager->flush();
        // Send an email to user
        $subject = 'Password Reset';
        $httpHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
        $passwordResetUrl = 'http://' . $httpHost . '/set-password?token=' . $token . "&email=" . $user->getEmail();
        // Produce HTML of password reset email
        $bodyHtml = $this->viewRenderer->render(
            'user/email/reset-password-email',
            [
                'passwordResetUrl' => $passwordResetUrl,
            ]
        );
        $html = new MimePart($bodyHtml);
        $html->type = "text/html";
        $body = new MimeMessage();
        $body->addPart($html);
        $mail = new Mail\Message();
        $mail->setEncoding('UTF-8');
        $mail->setBody($body);
        $mail->setFrom('no-reply@example.com', 'User Demo');
        $mail->addTo($user->getEmail(), $user->getFullName());
        $mail->setSubject($subject);

        $options = new SmtpOptions($this->config['smtp']);
        $this->getTransport()->setOptions($options);
        try {
            $this->getTransport()->send($mail);
        } catch (Exception $e) {
            $message = 'Error: Message - ' . $e->getMessage()
                . ', in file - ' . $e->getFile()
                . ', in line - ' . $e->getLine();
            $this->logger->err($message);
        }
    }

    /**
     * Checks whether the given password reset token is a valid one
     * @param string $email
     * @param string $passwordResetToken
     *
     * @return bool
     */
    public function validatePasswordResetToken(string $email, string $passwordResetToken): bool
    {
        /** @var User|null $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if (empty($user) || $user->getStatus() != User::STATUS_ACTIVE_ID) {
            return false;
        }
        $bcrypt = new Bcrypt();
        $tokenHash = $user->getPasswordResetToken();
        if (! $bcrypt->verify($passwordResetToken, $tokenHash)) {
            return false;
        }
        // Check that token was created not too long ago
        $tokenCreationDate = $user->getPasswordResetTokenCreationDate();
        $tokenCreationDate = $tokenCreationDate->getTimestamp();
        $currentDate = strtotime('now');
        if ($currentDate - $tokenCreationDate > 24 * 60 * 60) {
            return false; // expired
        }

        return true;
    }

    /**
     * This method sets new password by password reset token
     * @param string $email
     * @param string $passwordResetToken
     * @param string $newPassword
     *
     * @return bool
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function setNewPasswordByToken(string $email, string $passwordResetToken, string $newPassword): bool
    {
        if (! $this->validatePasswordResetToken($email, $passwordResetToken)) {
            return false;
        }
        /** @var User|null $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if (empty($user) || $user->getStatus() != User::STATUS_ACTIVE_ID) {
            return false;
        }
        $bcrypt = new Bcrypt();
        $passwordHash = $bcrypt->create($newPassword);
        $user->setPassword($passwordHash);
        $user->setPasswordResetToken(null);
        $user->setPasswordResetTokenCreationDate(null);
        $this->entityManager->flush();

        return true;
    }

    /**
     * This method is used to change the password for the given user. To change the password,
     * one must know the old password
     * @param User $user
     * @param array $data
     *
     * @return bool
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function changePassword(User $user, array $data): bool
    {
        $oldPassword = $data['old_password'];
        if (! $this->validatePassword($user, $oldPassword)) {
            return false;
        }
        $newPassword = $data['new_password'];
        if (strlen($newPassword) < 6 || strlen($newPassword) > 64) {
            return false;
        }
        $bcrypt = new Bcrypt();
        $passwordHash = $bcrypt->create($newPassword);
        $user->setPassword($passwordHash);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param int $page
     * @param int $countPerPage
     *
     * @return Paginator|null
     */
    public function getUsersPaginator(int $page, int $countPerPage): ?Paginator
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);
        $query = $userRepository->findUsersAccess();
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        /** @phpstan-ignore-next-line */
        $paginator = new Paginator($adapter);
        $paginator->setDefaultItemCountPerPage($countPerPage);
        $paginator->setCurrentPageNumber($page);

        return $paginator;
    }

    /**
     * @return void
     * @throws Exception
     */
    private function buildProducerKafka(): void
    {
        $this->producerKafka = new ProducerKafka($this->config, $this->logger);
    }

    /**
     * @return ProducerKafka
     * @throws Exception
     */
    public function getProducerKafka(): ProducerKafka
    {
        if (empty($this->producerKafka)) {
            $this->buildProducerKafka();
        }

        return $this->producerKafka;
    }

    /**
     * @param ProducerKafka $producerKafka
     *
     * @return $this
     */
    public function setProducerKafka(ProducerKafka $producerKafka): self
    {
        $this->producerKafka = $producerKafka;

        return $this;
    }

    /**
     * @param PermissionManager $permissionManager
     */
    public function setPermissionManager(PermissionManager $permissionManager): void
    {
        $this->permissionManager = $permissionManager;
    }

    /**
     * @param RoleManager $roleManager
     */
    public function setRoleManager(RoleManager $roleManager): void
    {
        $this->roleManager = $roleManager;
    }

    /**
     * @return SmtpTransport
     * @throws Exception
     */
    public function getTransport(): SmtpTransport
    {
        if (empty($this->transport)) {
            $this->buildTransport();
        }

        return $this->transport;
    }

    /**
     * @param SmtpTransport $transport
     */
    public function setTransport(SmtpTransport $transport): void
    {
        $this->transport = $transport;
    }

    /**
     * @return void
     * @throws Exception
     */
    private function buildTransport(): void
    {
        $this->transport = new SmtpTransport();
    }

    /**
     * @param int $length
     * @param string $charlist
     *
     * @return string
     */
    public static function getRandomToken(int $length = 32, string $charlist = '0123456789abcdefghijklmnopqrstuvwxyz'): string
    {
        return Rand::getString($length, $charlist);
    }
}
