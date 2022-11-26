<?php

namespace Xua\Core\Services;

use PDO;
use Xua\Core\Eves\Entity;
use Xua\Core\Eves\Service;
use Xua\Core\Tools\Entity\Database;
use Xua\Core\Tools\Entity\TableScheme;

final class EntityAlterService extends Service
{
    public static function alters(): string
    {
        return
            self::alterTransaction() .
            self::alterTimezone() .
            self::altersInDirs(ConstantService::get('config', 'paths.entities'));
    }

    private static function altersInDirs(array $dirs): string
    {
        $entities = ReflectionService::getClassesInDirs($dirs, Entity::class, false);
        $alters = [];
        $newTables = [];
        foreach ($entities as $entity) {
            $tableNamesAndAlter = $entity::alter();
            $newTables = array_merge($newTables, $tableNamesAndAlter['tableNames']);
            if ($tableNamesAndAlter['alters']) {
                $alters[] = $tableNamesAndAlter['alters'];
            }
        }
        $alters[] = TableScheme::getDropTables($newTables);
        return trim(implode(PHP_EOL . PHP_EOL, $alters));
    }

    private static function alterTransaction(): string
    {
        $expectedTransactionIsolationLevel = ConstantService::get('config', 'db.isolationLevel');
        $realTransactionIsolationLevel = Entity::execute('select @@global.transaction_isolation')->fetch(PDO::FETCH_NUM)[0];
        if ($realTransactionIsolationLevel != $expectedTransactionIsolationLevel) {
            $expectedTransactionIsolationLevelSyntax = Database::transactionIsolationLevel($expectedTransactionIsolationLevel);
            return "set global transaction isolation level $expectedTransactionIsolationLevelSyntax;" . PHP_EOL;
        }
        return '';
    }

    private static function alterTimezone(): string
    {
        $expectedTimezone = ConstantService::get('config', 'services.ll.timezone');
        $realTimezone = Entity::execute('select @@global.time_zone')->fetch(PDO::FETCH_NUM)[0];
        if ($realTimezone != $expectedTimezone) {
            return "set global time_zone='$expectedTimezone';" . PHP_EOL;
        }
        return '';
    }
}