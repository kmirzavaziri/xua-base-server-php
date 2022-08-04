<?php

namespace Xua\Core\Services;

use PDO;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use RegexIterator;
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
        $entities = [];
        foreach ($dirs as $dir) {
            $phpFiles = new RegexIterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)), '/\.php$/');
            foreach ($phpFiles as $phpFile) {
                $entities[] = self::getClassName(file_get_contents($phpFile->getRealPath()));
            }
        }
        $alters = [];
        $newTables = [];
        foreach ($entities as $entity) {
            if ($entity and is_a($entity, Entity::class, true) and !(new ReflectionClass($entity))->isAbstract()) {
                $tableNamesAndAlter = $entity::alter();
                $newTables = array_merge($newTables, $tableNamesAndAlter['tableNames']);
                if ($tableNamesAndAlter['alters']) {
                    $alters[] = $tableNamesAndAlter['alters'];
                }
            }
        }
        $alters[] = TableScheme::getDropTables($newTables);
        return trim(implode(PHP_EOL . PHP_EOL, $alters));
    }

    private static function getClassName(string $phpCode): ?string
    {
        $tokens = token_get_all($phpCode);
        $namespace = '';
        for ($index = 0; isset($tokens[$index]); $index++) {
            if (!isset($tokens[$index][0])) {
                continue;
            }
            if ($tokens[$index][0] === T_NAMESPACE) {
                $index += 2;
                while (isset($tokens[$index]) && is_array($tokens[$index])) {
                    $namespace .= $tokens[$index++][1];
                }
            }
            if ($tokens[$index][0] === T_CLASS && $tokens[$index + 1][0] === T_WHITESPACE && $tokens[$index + 2][0] === T_STRING) {
                $index += 2;
                return $namespace.'\\'.$tokens[$index][1];
            }
        }
        return null;
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