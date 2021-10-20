<?php


namespace Xua\Core\Services;

use PDO;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use Xua\Core\Eves\Entity;
use Xua\Core\Eves\Service;
use Xua\Core\Tools\Entity\Database;
use Xua\Core\Tools\Entity\TableScheme;

final class EntityAlterService extends Service
{
    public static function alters(): string
    {
        return self::alterTransaction() . self::altersInDir(ConstantService::ENTITIES_DIR);
    }

    private static function altersInDir(string $dir): string
    {
        $allFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        $phpFiles = new RegexIterator($allFiles, '/\.php$/');

        $alters = [];
        $newTables = [];

        foreach ($phpFiles as $phpFile) {
            $className = self::getClassName(file_get_contents($phpFile->getRealPath()));
            // @TODO check className is not abstract
            if ($className and is_a($className, Entity::class, true)) {
                $tableNamesAndAlter = $className::alter();
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
        $expectedTransactionIsolationLevel = ConstantService::DB_TRANSACTION_ISOLATION_LEVEL;
        $realTransactionIsolationLevel = Entity::execute('SELECT @@GLOBAL.TRANSACTION_ISOLATION')->fetch(PDO::FETCH_NUM)[0];
        if ($realTransactionIsolationLevel != $expectedTransactionIsolationLevel) {
            $expectedTransactionIsolationLevelSyntax = Database::transactionIsolationLevel($expectedTransactionIsolationLevel);
            return "SET GLOBAL TRANSACTION ISOLATION LEVEL $expectedTransactionIsolationLevelSyntax;" . PHP_EOL;
        }
        return '';
    }
}