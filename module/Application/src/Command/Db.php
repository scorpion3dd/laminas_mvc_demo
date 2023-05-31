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

namespace Application\Command;

use Application\Document\Log;
use Application\Repository\LogsRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Fixtures\RoleFixtures;
use Redis;
use User\Entity\Role;

/**
 * Class Db
 * @package Application\Command
 */
class Db
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param string $file
     * @return void
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function dropMySql(EntityManagerInterface $entityManager, string $file = ''): void
    {
        // @codeCoverageIgnoreStart
        $fileSql = __DIR__ . "/../../../../data/db/$file";
        if (! file_exists($fileSql)) {
            throw new Exception($file . ' - file not exists');
        }
        /** @var string $sql */
        $sql = file_get_contents($fileSql);
        $stmt = $entityManager->getConnection()->prepare($sql);
        $stmt->executeStatement();
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param EntityManagerInterface $entityManager
     *
     * @return string
     */
    public function getMySqlDbName(EntityManagerInterface $entityManager): string
    {
        // @codeCoverageIgnoreStart
        return $entityManager->getConnection()->getDatabase();
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param Redis $redis
     * @param EntityManagerInterface $entityManager
     * @param string $type
     *
     * @return void
     */
    public function dropRedis(Redis $redis, EntityManagerInterface $entityManager, string $type = ''): void
    {
        if ($type == AbstractCommand::TYPE_INTEGRATION) {
            $redis->del(RoleFixtures::REDIS_SETS_ROLES_INTEGRATION);
            $redis->del(RoleFixtures::REDIS_ROLE_SET_INTEGRATION);
        } else {
            $redis->del(RoleFixtures::REDIS_SETS_ROLES);
            $redis->del(RoleFixtures::REDIS_ROLE_SET);
        }
        try {
            /** @var array|null $roles */
            $roles = $entityManager->getRepository(Role::class)->findBy([], ['id' => 'ASC']);
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $roles = [];
        }
        // @codeCoverageIgnoreEnd
        /** @var Role $role */
        foreach ($roles as $role) {
            if ($type == AbstractCommand::TYPE_INTEGRATION) {
                $redis->del(RoleFixtures::REDIS_ROLE_INTEGRATION . $role->getId());
            } else {
                $redis->del(RoleFixtures::REDIS_ROLE . $role->getId());
            }
        }
    }

    /**
     * @param DocumentManager $documentManager
     *
     * @return void
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function dropMongo(DocumentManager $documentManager): void
    {
        /** @var LogsRepository $logRepository */
        $logRepository = $documentManager->getRepository(Log::class);
        $logRepository->deleteAllLogs();
    }
}
