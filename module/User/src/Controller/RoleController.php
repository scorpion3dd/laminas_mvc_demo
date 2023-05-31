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
use User\Controller\Plugin\CurrentUserPlugin;
use User\Service\RoleManager;
use Laminas\Form\Element\Select;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Log\Logger;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\View\Model\ViewModel;
use User\Entity\Role;
use User\Entity\Permission;
use User\Form\RoleForm;
use User\Form\RolePermissionsForm;

/**
 * This controller is responsible for role management (adding, editing, viewing, deleting)
 * Class RoleController
 * @package User\Controller
 * @method FlashMessenger flashMessenger()
 * @method CurrentUserPlugin currentUser()
 */
class RoleController extends AbstractActionController
{
    /** @var EntityManager $entityManager */
    private EntityManager $entityManager;

    /** @var RoleManager $roleManager */
    private RoleManager $roleManager;

    /** @var Logger $logger */
    protected Logger $logger;

    /**
     * RoleController constructor
     * @param EntityManager $entityManager
     * @param RoleManager $roleManager
     * @param Logger $logger
     */
    public function __construct(EntityManager $entityManager, RoleManager $roleManager, Logger $logger)
    {
        $this->entityManager = $entityManager;
        $this->roleManager = $roleManager;
        $this->logger = $logger;
    }

    /**
     * This is the default "index" action of the controller. It displays the list of roles
     *
     * @return ViewModel
     * @throws Exception
     */
    public function indexAction(): ViewModel
    {
        $roles = $this->roleManager->rolesGetFromQueueRedis();
        if (empty($roles)) {
            $roles = $this->entityManager->getRepository(Role::class)->findBy([], ['id' => 'ASC']);
            $this->roleManager->rolesPushToQueueRedis($roles);
        }

        return new ViewModel([
            'roles' => $roles
        ]);
    }

    /**
     * This action displays a page allowing to add a new role
     *
     * @return Response|ViewModel
     * @throws Exception
     */
    public function addAction(): Response|ViewModel
    {
        $form = new RoleForm('create', $this->entityManager);
        $roleList = [];
        $roles = $this->entityManager->getRepository(Role::class)->findBy([], ['name' => 'ASC']);
        /** @var Role $role */
        foreach ($roles as $role) {
            $roleList[$role->getId()] = $role->getName();
        }
        /** @var Select $roles */
        $roles = $form->get('inherit_roles');
        $roles->setValueOptions($roleList);
        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                /** @var array $data */
                $data = $form->getData();
                $role = $this->roleManager->addRole($data);
                $this->roleManager->roleSetRedis($role);
                $this->roleManager->rolePushToQueueRedis($role);
                $this->flashMessenger()->addSuccessMessage('Added new role.');

                return $this->redirect()->toRoute('roles', ['action' => 'index']);
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
     * The "view" action displays a page allowing to view role's details
     *
     * @return void|ViewModel
     * @throws Exception
     */
    public function viewAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id < 1) {
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(404);

            return;
        }
        if ($this->roleManager->roleCheckRedis($id)) {
            /** @var Role|null $role */
            $role = $this->roleManager->roleGetRedis($id);
        }
        if (empty($role)) {
            /** @var Role|null $role */
            $role = $this->entityManager->getRepository(Role::class)->find($id);
            $this->roleManager->roleSetRedis($role);
        }
        if (empty($role)) {
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(404);

            return;
        }
        $allPermissions = $this->entityManager->getRepository(Permission::class)->findBy([], ['name' => 'ASC']);
        $effectivePermissions = $this->roleManager->getEffectivePermissions($role);

        return new ViewModel([
            'role' => $role,
            'allPermissions' => $allPermissions,
            'effectivePermissions' => $effectivePermissions
        ]);
    }

    /**
     * This action displays a page allowing to edit an existing role
     *
     * @return void|Response|ViewModel
     * @throws Exception
     */
    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id < 1) {
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(404);

            return;
        }
        /** @var Role|null $role */
        $role = $this->entityManager->getRepository(Role::class)->find($id);
        if (empty($role)) {
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(404);

            return;
        }
        $form = new RoleForm('update', $this->entityManager, $role);
        $roleList = [];
        $selectedRoles = [];
        $roles = $this->entityManager->getRepository(Role::class)->findBy([], ['name' => 'ASC']);
        /** @var Role $role2 */
        foreach ($roles as $role2) {
            if ($role2->getId() == $role->getId()) {
                continue;
            }
            $roleList[$role2->getId()] = $role2->getName();
            if ($role->hasParent($role2)) {
                $selectedRoles[] = $role2->getId();
            }
        }
        /** @var Select $roles */
        $roles = $form->get('inherit_roles');
        $roles->setValueOptions($roleList);
        $roles->setValue($selectedRoles);
        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                /** @var array $data */
                $data = $form->getData();
                $role = $this->roleManager->updateRole($role, $data);
                $this->roleManager->roleSetRedis($role);
                $this->flashMessenger()->addSuccessMessage('Updated the role.');

                return $this->redirect()->toRoute('roles', ['action' => 'index']);
            } else {
                $message = $form->getMessages();
                $this->logger->log(Logger::ERR, $message);
            }
        } else {
            $form->setData([
                'name' => $role->getName(),
                'description' => $role->getDescription()
            ]);
        }

        return new ViewModel([
            'form' => $form,
            'role' => $role
        ]);
    }

    /**
     * The "editPermissions" action allows to edit permissions assigned to the given role
     *
     * @return void|Response|ViewModel
     * @throws Exception
     */
    public function editPermissionsAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id < 1) {
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(404);

            return;
        }
        /** @var Role|null $role */
        $role = $this->entityManager->getRepository(Role::class)->find($id);
        if (empty($role)) {
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(404);

            return;
        }
        $allPermissions = $this->entityManager->getRepository(Permission::class)->findBy([], ['name' => 'ASC']);
        $effectivePermissions = $this->roleManager->getEffectivePermissions($role);
        $form = new RolePermissionsForm();
        foreach ($allPermissions as $permission) {
            /** @var Permission $permission */
            $label = $permission->getName();
            $isDisabled = false;
            if (isset($effectivePermissions[$permission->getName()])
                && $effectivePermissions[$permission->getName()] == 'inherited'
            ) {
                $label .= ' (inherited)';
                $isDisabled = true;
            }
            $form->addPermissionField($permission->getName(), $label, $isDisabled);
        }
        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                /** @var array $data */
                $data = $form->getData();
                $this->roleManager->updateRolePermissions($role, $data);
                $this->flashMessenger()->addSuccessMessage('Updated permissions for the role.');

                return $this->redirect()->toRoute('roles', ['action' => 'view', 'id' => $role->getId()]);
            } else {
                $message = $form->getMessages();
                $this->logger->log(Logger::ERR, $message);
            }
        } else {
            $data = [];
            foreach ($effectivePermissions as $name => $inherited) {
                $data['permissions'][$name] = 1;
            }
            $form->setData($data);
        }
        $errors = $form->getMessages();

        return new ViewModel([
            'form' => $form,
            'role' => $role,
            'allPermissions' => $allPermissions,
            'effectivePermissions' => $effectivePermissions
        ]);
    }

    /**
     * This action deletes a permission
     *
     * @return void|Response
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id < 1) {
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(404);

            return;
        }
        /** @var Role|null $role */
        $role = $this->entityManager->getRepository(Role::class)->find($id);
        if (empty($role)) {
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(404);

            return;
        }
        $this->roleManager->deleteRole($role);
        $this->flashMessenger()->addSuccessMessage('Deleted the role.');

        return $this->redirect()->toRoute('roles', ['action' => 'index']);
    }
}
