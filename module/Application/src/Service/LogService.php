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

namespace Application\Service;

use Application\Document\Log;
use Application\Repository\LogsRepository;
use Carbon\Carbon;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Entity\User;
use Laminas\Paginator\Paginator;

/**
 * This service class is used to deliver an E-mail message to recipient.
 * @package Application\Service
 */
class LogService extends AbstractService
{
    public const COUNT_PER_PAGE = 7;

    /** @var DocumentManager $dm */
    private $dm;

    /**
     * LogService constructor
     * @param ContainerInterface $container
     * @param DocumentManager $dm
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container, $dm)
    {
        parent::__construct($container);
        $this->dm = $dm;
    }

    /**
     * @param User|null $currentUser
     * @param array $data
     *
     * @return Log
     */
    public function addLog(?User $currentUser, array $data): Log
    {
        $log = new Log();
        $currentUserId = ! empty($currentUser) ? $currentUser->getId() : 0;
        $currentUserId = (string)$currentUserId;
        $log->setExtra(['currentUserId=' . $currentUserId]);
        $log->setMessage($data['message']);
        $log->setTimestamp(Carbon::parse($data['timestamp']));
        $log->setPriority($data['priority']);
        $priorityList = Log::getPriorities();
        $log->setPriorityName($priorityList[$data['priority']]);
        $this->dm->persist($log);
        $this->dm->flush();

        return $log;
    }

    /**
     * @param Log $log
     * @param User|null $currentUser
     * @param array $data
     *
     * @return bool
     */
    public function updateLog(Log $log, ?User $currentUser, array $data): bool
    {
        $currentUserId = ! empty($currentUser) ? $currentUser->getId() : 0;
        $currentUserId = (string)$currentUserId;
        $log->setExtra(['currentUserId=' . $currentUserId]);
        $log->setMessage($data['message']);
        $log->setPriority($data['priority']);
        $priorityList = Log::getPriorities();
        $log->setPriorityName($priorityList[$data['priority']]);
        $this->dm->flush();

        return true;
    }

    /**
     * @param Log $log
     *
     * @return bool
     */
    public function removeLog(Log $log): bool
    {
        $this->dm->remove($log);
        $this->dm->flush();

        return true;
    }

    /**
     * @param string $id
     *
     * @return object|null
     */
    public function getLog(string $id): ?object
    {
        return $this->dm->getRepository(Log::class)->findOneBy(['id' => $id]);
    }

    /**
     * @param int $count
     *
     * @return array|null
     */
    public function getLogs(int $count = 5): ?array
    {
        return $this->dm->getRepository(Log::class)->findBy([], ['id' => 'ASC'], $count);
    }

    /**
     * @param int $page
     *
     * @return Paginator|null
     */
    public function getLogsPaginator(int $page): ?Paginator
    {
        /** @var LogsRepository $logRepository */
        $logRepository = $this->dm->getRepository(Log::class);
        $query = $logRepository->findAllLogs();
        /** @phpstan-ignore-next-line */
        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        /** @phpstan-ignore-next-line */
        $paginator = new Paginator($adapter);
        $paginator->setDefaultItemCountPerPage(self::COUNT_PER_PAGE);
        $paginator->setCurrentPageNumber($page);

        return $paginator;
    }
}
