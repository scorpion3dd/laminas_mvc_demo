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
use User\Service\PermissionManager;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Log\Logger;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\View\Model\ViewModel;
use User\Entity\Permission;
use User\Form\PermissionForm;

/**
 * This controller is responsible for permission management (adding, editing, viewing, deleting)
 * Class PermissionController
 * @package User\Controller
 * @method FlashMessenger flashMessenger()
 */
class PermissionController extends AbstractActionController
{
    /** @var EntityManager $entityManager */
    private EntityManager $entityManager;

    /** @var PermissionManager $permissionManager */
    private PermissionManager $permissionManager;

    /** @var Logger $logger */
    protected Logger $logger;

    /**
     * PermissionController constructor
     * @param EntityManager $entityManager
     * @param PermissionManager $permissionManager
     * @param Logger $logger
     */
    public function __construct(EntityManager $entityManager, PermissionManager $permissionManager, Logger $logger)
    {
        $this->entityManager = $entityManager;
        $this->permissionManager = $permissionManager;
        $this->logger = $logger;
    }

    /**
     * This is the default "index" action of the controller. It displays the list of permission
     *
     * @return ViewModel
     */
    public function indexAction(): ViewModel
    {
        $permissions = $this->entityManager->getRepository(Permission::class)->findBy([], ['name' => 'ASC']);

        return new ViewModel([
            'permissions' => $permissions
        ]);
    }

    /**
     * This action displays a page allowing to add a new permission
     *
     * @return ViewModel|Response
     * @throws Exception
     */
    public function addAction(): ViewModel|Response
    {
        $form = new PermissionForm('create', $this->entityManager);
        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                /** @var array $data */
                $data = $form->getData();
                $this->permissionManager->addPermission($data);
                $this->flashMessenger()->addSuccessMessage('Added new permission.');

                return $this->redirect()->toRoute('permissions', ['action' => 'index']);
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
     * The "view" action displays a page allowing to view permission's details
     *
     * @return null|ViewModel
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
        $permission = $this->entityManager->getRepository(Permission::class)->find($id);
        if (empty($permission)) {
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(404);

            return null;
        }

        return new ViewModel([
            'permission' => $permission
        ]);
    }

    /**
     * This action displays a page allowing to edit an existing permission
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
        /** @var Permission|null $permission */
        $permission = $this->entityManager->getRepository(Permission::class)->find($id);
        if (empty($permission)) {
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(404);

            return;
        }
        $form = new PermissionForm('update', $this->entityManager, $permission);
        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                /** @var array $data */
                $data = $form->getData();
                $this->permissionManager->updatePermission($permission, $data);
                $this->flashMessenger()->addSuccessMessage('Updated the permission.');

                return $this->redirect()->toRoute('permissions', ['action' => 'index']);
            } else {
                $message = $form->getMessages();
                $this->logger->log(Logger::ERR, $message);
            }
        } else {
            $form->setData([
                'name' => $permission->getName(),
                'description' => $permission->getDescription()
            ]);
        }

        return new ViewModel([
            'form' => $form,
            'permission' => $permission
        ]);
    }

    /**
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
        /** @var Permission|null $permission */
        $permission = $this->entityManager->getRepository(Permission::class)->find($id);
        if (empty($permission)) {
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(404);

            return;
        }
        $this->permissionManager->deletePermission($permission);
        $this->flashMessenger()->addSuccessMessage('Deleted the permission.');

        return $this->redirect()->toRoute('permissions', ['action' => 'index']);
    }
}
