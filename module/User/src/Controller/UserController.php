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

namespace User\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use User\Controller\Plugin\AccessPlugin;
use User\Controller\Plugin\CurrentUserPlugin;
use User\Service\UserManager;
use Laminas\Form\Element\Select;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Log\Logger;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\View\Model\ViewModel;
use User\Entity\User;
use User\Entity\Role;
use User\Form\UserForm;
use User\Form\PasswordChangeForm;
use User\Form\PasswordResetForm;

/**
 * This controller is responsible for user management (adding, editing,
 * viewing users and changing user's password)
 * Class UserController
 * @package User\Controller
 * @method AccessPlugin access()
 * @method FlashMessenger flashMessenger()
 * @method CurrentUserPlugin currentUser()
 */
class UserController extends AbstractActionController
{
    public const COUNT_PER_PAGE = 7;

    /** @var EntityManager $entityManager */
    private EntityManager $entityManager;

    /** @var UserManager $userManager */
    private UserManager $userManager;

    /** @var Logger $logger */
    protected Logger $logger;

    /**
     * UserController constructor
     * @param EntityManager $entityManager
     * @param UserManager $userManager
     * @param Logger $logger
     */
    public function __construct(EntityManager $entityManager, UserManager $userManager, Logger $logger)
    {
        $this->entityManager = $entityManager;
        $this->userManager = $userManager;
        $this->logger = $logger;
    }

    /**
     * This is the default "index" action of the controller. It displays the list of users
     *
     * @return ViewModel|null
     */
    public function indexAction(): ?ViewModel
    {
        $page = (int)$this->params()->fromQuery('page', 0);
        /** @phpstan-ignore-next-line */
        if (! $this->access('user.manage')) {
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(401);

            return null;
        }
        $users = $this->userManager->getUsersPaginator($page, self::COUNT_PER_PAGE);

        return new ViewModel([
            'users' => $users
        ]);
    }

    /**
     * This action displays a page allowing to add a new user
     *
     * @return ViewModel|Response
     * @throws Exception
     */
    public function addAction(): ViewModel|Response
    {
        $form = new UserForm('create', $this->entityManager);
        $allRoles = $this->entityManager->getRepository(Role::class)->findBy([], ['name' => 'ASC']);
        $roleList = [];
        /** @var Role $role */
        foreach ($allRoles as $role) {
            $roleList[$role->getId()] = $role->getName();
        }
        /** @var Select $roles */
        $roles = $form->get('roles');
        $roles->setValueOptions($roleList);
        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                /** @var array $data */
                $data = $form->getData();
                /** @var User $currentUser */
                $currentUser = $this->currentUser();
                $user = $this->userManager->addUser($currentUser, $data);

                return $this->redirect()->toRoute(
                    'users',
                    [
                        'action' => 'view',
                        'id' => $user->getId()
                    ]
                );
            } else {
                // @codeCoverageIgnoreStart
                $message = $form->getMessages();
                $this->logger->log(Logger::ERR, $message);
                // @codeCoverageIgnoreEnd
            }
        }

        return new ViewModel([
            'form' => $form
        ]);
    }

    /**
     * The "view" action displays a page allowing to view user's details
     *
     * @return ViewModel|null
     */
    public function viewAction(): ?ViewModel
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id < 1) {
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(404);

            return null;
        }
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (empty($user)) {
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(404);

            return null;
        }

        return new ViewModel([
            'user' => $user
        ]);
    }

    /**
     * The "edit" action displays a page allowing to edit user
     *
     * @return Response|ViewModel|null
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function editAction(): Response|ViewModel|null
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id < 1) {
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(404);

            return null;
        }
        /** @var User|null $user */
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (empty($user)) {
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(404);

            return null;
        }
        $form = new UserForm('update', $this->entityManager, $user);
        $allRoles = $this->entityManager->getRepository(Role::class)->findBy([], ['name' => 'ASC']);
        $roleList = [];
        /** @var Role $role */
        foreach ($allRoles as $role) {
            $roleList[$role->getId()] = $role->getName();
        }
        /** @var Select $roles */
        $roles = $form->get('roles');
        $roles->setValueOptions($roleList);
        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                /** @var array $data */
                $data = $form->getData();
                /** @var User $currentUser */
                $currentUser = $this->currentUser();
                $this->userManager->updateUser($user, $currentUser, $data);

                return $this->redirect()->toRoute(
                    'users',
                    [
                        'action' => 'view',
                        'id' => $user->getId()
                    ]
                );
            } else {
                $message = $form->getMessages();
                $this->logger->log(Logger::ERR, $message);
            }
        } else {
            $userRoleIds = [];
            foreach ($user->getRoles() as $role) {
                $userRoleIds[] = $role->getId();
            }
            $form->setData([
                'full_name' => $user->getFullName(),
                'description' => $user->getDescription(),
                'date_birthday' => $user->getDateBirthday(),
                'email' => $user->getEmail(),
                'status' => $user->getStatus(),
                'access' => $user->getAccess(),
                'gender' => $user->getGender(),
                'roles' => $userRoleIds
            ]);
        }

        return new ViewModel([
            'user' => $user,
            'form' => $form
        ]);
    }

    /**
     * This action displays a page allowing to change user's password
     *
     * @return ViewModel|Response|null
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function changePasswordAction(): ViewModel|Response|null
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id < 1) {
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(404);

            return null;
        }
        /** @var User|null $user */
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (empty($user)) {
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(404);

            return null;
        }
        $form = new PasswordChangeForm('change');
        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                /** @var array $data */
                $data = $form->getData();
                if (! $this->userManager->changePassword($user, $data)) {
                    $this->flashMessenger()
                        ->addErrorMessage('Sorry, the old password is incorrect. Could not set the new password.');
                } else {
                    $this->flashMessenger()
                        ->addSuccessMessage('Changed the password successfully.');
                }

                return $this->redirect()->toRoute(
                    'users',
                    [
                        'action' => 'view',
                        'id' => $user->getId()
                    ]
                );
            } else {
                $message = $form->getMessages();
                $this->logger->log(Logger::ERR, $message);
            }
        }

        return new ViewModel([
            'user' => $user,
            'form' => $form
        ]);
    }

    /**
     * This action displays the "Reset Password" page
     *
     * @return Response|ViewModel
     * @throws Exception
     */
    public function resetPasswordAction()
    {
        $form = new PasswordResetForm();
        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            /** @var array $data */
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                /** @var User $user */
                $user = $this->entityManager->getRepository(User::class)
                    ->findOneBy(['email' => $data['email']]);
                if ($user != null && $user->getStatus() == User::STATUS_ACTIVE_ID) {
                    $this->userManager->generatePasswordResetToken($user);

                    return $this->redirect()->toRoute('users', ['action' => 'message', 'id' => 'sent']);
                } else {
                    return $this->redirect()->toRoute('users', ['action' => 'message', 'id' => 'invalid-email']);
                }
            } else {
                $message = $form->getMessages();
                $this->logger->log(Logger::ERR, $message);
            }
        }

        return new ViewModel([
            'form' => $form
        ]);
    }

    /**
     * This action displays an informational message page.
     * For example "Your password has been resetted" and so on
     *
     * @return ViewModel
     * @throws Exception
     */
    public function messageAction(): ViewModel
    {
        $id = (string)$this->params()->fromRoute('id');
        if ($id != 'invalid-email' && $id != 'sent' && $id != 'set' && $id != 'failed') {
            $message = 'Invalid message ID specified';
            $this->logger->log(Logger::ERR, $message);
            throw new Exception($message);
        }

        return new ViewModel([
            'id' => $id
        ]);
    }

    /**
     * This action displays the "Reset Password" page
     *
     * @return Response|ViewModel
     * @throws Exception
     */
    public function setPasswordAction()
    {
        $email = $this->params()->fromQuery('email', null);
        $passwordResetToken = $this->params()->fromQuery('token', null);
        if (! empty($passwordResetToken) && (! is_string($passwordResetToken) || strlen($passwordResetToken) > 132)) {
            $message = 'Invalid token type or length';
            $this->logger->log(Logger::ERR, $message);
            throw new Exception($message);
        }
        if (empty($passwordResetToken) ||
           ! $this->userManager->validatePasswordResetToken($email, $passwordResetToken)) {
            return $this->redirect()->toRoute(
                'users',
                [
                    'action' => 'message',
                    'id' => 'failed'
                ]
            );
        }
        $form = new PasswordChangeForm('reset');
        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                /** @var array $data */
                $data = $form->getData();
                if ($this->userManager->setNewPasswordByToken($email, $passwordResetToken, $data['new_password'])) {
                    return $this->redirect()->toRoute(
                        'users',
                        [
                            'action' => 'message',
                            'id' => 'set'
                        ]
                    );
                } else {
                    return $this->redirect()->toRoute(
                        'users',
                        [
                            'action' => 'message',
                            'id' => 'failed'
                        ]
                    );
                }
            } else {
                $message = $form->getMessages();
                $this->logger->log(Logger::ERR, $message);
            }
        }

        return new ViewModel([
            'form' => $form
        ]);
    }
}
