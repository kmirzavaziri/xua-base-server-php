<?php

namespace Xua\Core\Services;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use RegexIterator;

class ReflectionService extends \Xua\Core\Eves\Service
{
    /**
     * @param string[] $dirs
     * @return string[]
     */
    public static function getClassesInDirs(array $dirs, string $parentClass, bool $includeAbstractClasses = true): array
    {
        $classes = [];
        foreach ($dirs as $dir) {
            $phpFiles = new RegexIterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)), '/\.php$/');
            foreach ($phpFiles as $phpFile) {
                $class = self::getClassName($phpFile->getRealPath());
                $condition = ($class and is_a($class, $parentClass, true));
                if (!$includeAbstractClasses) {
                    $condition = ($condition and !(new ReflectionClass($class))->isAbstract());
                }
                if ($condition) {
                    $classes[] = $class;
                }
            }
        }
        return $classes;
    }

    public static function getClassName(string $path): ?string
    {
        $tokens = token_get_all(file_get_contents($path));
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
                return $namespace . '\\' . $tokens[$index][1];
            }
        }
        return null;
    }

}