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

namespace Application\Controller;

use Application\Form\LogForm;
use Application\Service\LogService;
use Application\Document\Log;
use User\Controller\Plugin\CurrentUserPlugin;
use User\Entity\User;
use Laminas\Form\Element\Select;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Log\Logger;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\View\Model\ViewModel;

/**
 * This controller is Logs in MongoDB
 * Class ConsumerController
 * @package Application\Controller
 * @method FlashMessenger flashMessenger()
 * @method CurrentUserPlugin currentUser()
 */
class LogsController extends AbstractActionController
{
    /** @var Logger $logger */
    protected Logger $logger;

    /** @var LogService $logService */
    private LogService $logService;

    /**
     * LogsController constructor
     * @param Logger $logger
     * @param LogService $logService
     */
    public function __construct(Logger $logger, LogService $logService)
    {
        $this->logger = $logger;
        $this->logService = $logService;
    }

    /**
     * This is the default "index" action of the controller. It displays the list of users
     *
     * @return ViewModel
     */
    public function indexAction(): ViewModel
    {
        $logs = $this->logService->getLogs();
        if (empty($logs)) {
            $page = (int)$this->params()->fromQuery('page', 0);
            $logs = $this->logService->getLogsPaginator($page);
        }

        return new ViewModel([
            'logs' => $logs
        ]);
    }

    /**
     * The "view" action displays a page allowing to view log's details
     *
     * @return ViewModel|null
     */
    public function viewAction(): ?ViewModel
    {
        $id = (string)$this->params()->fromRoute('id', '');
        if ($id == '') {
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(404);

            return null;
        }
        /** @var Log|null $log */
        $log = $this->logService->getLog($id);
        if (empty($log)) {
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(404);

            return null;
        }

        return new ViewModel([
            'log' => $log
        ]);
    }

    /**
     * @return ViewModel|Response
     */
    public function addAction(): ViewModel|Response
    {
        $form = new LogForm('create');
        $priorityList = Log::getPriorities();
        /** @var Select $priority */
        $priority = $form->get('priority');
        $priority->setValueOptions($priorityList);

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
                $log = $this->logService->addLog($currentUser, $data);

                return $this->redirect()->toRoute(
                    'logs',
                    [
                        'action' => 'view',
                        'id' => $log->getId()
                    ]
                );
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
     * The "edit" action displays a page allowing to edit user
     *
     * @return Response|ViewModel|null
     */
    public function editAction(): Response|ViewModel|null
    {
        $id = (string)$this->params()->fromRoute('id', '');
        if ($id == '') {
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(404);

            return null;
        }
        /** @var Log|null $log */
        $log = $this->logService->getLog($id);
        if (empty($log)) {
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(404);

            return null;
        }
        $form = new LogForm('update');
        $priorityList = Log::getPriorities();
        /** @var Select $priority */
        $priority = $form->get('priority');
        $priority->setValueOptions($priorityList);

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
                $this->logService->updateLog($log, $currentUser, $data);

                return $this->redirect()->toRoute(
                    'logs',
                    [
                        'action' => 'view',
                        'id' => $log->getId()
                    ]
                );
            } else {
                $message = $form->getMessages();
                $this->logger->log(Logger::ERR, $message);
            }
        } else {
            $form->setData([
                'message' => $log->getMessage(),
                'priority' => $log->getPriority(),
            ]);
        }

        return new ViewModel([
            'log' => $log,
            'form' => $form
        ]);
    }

    /**
     * This action deletes a log
     *
     * @return void|Response
     */
    public function deleteAction()
    {
        $id = (string)$this->params()->fromRoute('id', '');
        if ($id == '') {
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(404);

            return;
        }
        /** @var Log|null $log */
        $log = $this->logService->getLog($id);
        if (empty($log)) {
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(404);

            return;
        }
        $this->logService->removeLog($log);
        $this->flashMessenger()->addSuccessMessage('Deleted the log.');

        return $this->redirect()->toRoute('logs', ['action' => 'index']);
    }
}
