<?php


namespace XUA\Services;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use XUA\Eves\Entity;
use XUA\Eves\Service;
use XUA\Tools\Entity\TableScheme;

final class EntityAlterService extends Service
{
    public static function alters(): string
    {
        return self::altersInDir(ConstantService::ENTITIES_DIR);
    }

    private static function altersInDir(string $dir): string
    {
        $allFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        $phpFiles = new RegexIterator($allFiles, '/\.php$/');

        $alters = [];
        $newTables = [];

        foreach ($phpFiles as $phpFile) {
            $className = self::getClassName(file_get_contents($phpFile->getRealPath()));
            if ($className and is_a($className, Entity::class, true)) {
                $tableNamesAndAlter = $className::alter();
                $newTables = array_merge($newTables, $tableNamesAndAlter['tableNames']);
                if ($tableNamesAndAlter['alters']) {
                    $alters[] = $tableNamesAndAlter['alters'];
                }
            }
        }

        $alters[] = TableScheme::getDropTables($newTables);

        return implode(PHP_EOL . PHP_EOL, $alters);
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
}