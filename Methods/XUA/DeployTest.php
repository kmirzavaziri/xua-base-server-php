<?php

namespace Methods\XUA;


use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Services\XUA\ConstantService;
use Supers\Basics\Strings\Text;
use XUA\Entity;
use XUA\Method;
use XUA\Tools\Entity\TableScheme;
use XUA\Tools\Signature\MethodItemSignature;

/**
 * @property string alters
 * @method static MethodItemSignature R_alters() The Signature of: Response Item `alters`
 */
class DeployTest extends Method
{
    protected static function _requestSignatures(): array
    {
        return array_merge(parent::_requestSignatures(), [
        ]);
    }

    protected static function _responseSignatures(): array
    {
        return array_merge(parent::_responseSignatures(), [
            'alters' => new MethodItemSignature(new Text([]), true, null, false)
        ]);
    }

    protected function execute(): void
    {
        $entitiesIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(ConstantService::ENTITIES_NAMESPACE));
        $alters = [];
        $newTables = [];
        foreach ($entitiesIterator as $entityFile) {
            if ($entityFile->isFile() && $entityFile->getExtension() == 'php') {
                $entityFileName = $entityFile->getPathname();
                $class = str_replace(
                    '/',
                    '\\',
                    substr($entityFileName, 0, strlen($entityFileName) - 4)
                );
                if (is_a($class, Entity::class, true)) {
                    $tableNamesAndAlter = $class::alter();
                    $newTables = array_merge($newTables, $tableNamesAndAlter['tableNames']);
                    if ($tableNamesAndAlter['alters']) {
                        $alters[] = $tableNamesAndAlter['alters'];
                    }
                }
            }
        }

        $alters[] = TableScheme::getDropTables($newTables);

        $this->alters = implode(PHP_EOL . PHP_EOL, $alters);
    }
}