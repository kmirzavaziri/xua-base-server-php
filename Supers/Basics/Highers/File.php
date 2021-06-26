<?php


namespace Supers\Basics\Highers;



use Services\XUA\ExpressionService;
use Supers\Basics\Boolean;
use Supers\Basics\Strings\Enum;
use Supers\Basics\Strings\Text;
use XUA\Super;
use XUA\Tools\Signature\SuperArgumentSignature;

/**
 * @property ?string type
 * @method static SuperArgumentSignature A_type() The Signature of: Argument `type`
 * @property ?string storeExtension
 * @method static SuperArgumentSignature A_storeExtension() The Signature of: Argument `storeExtension`
 * @property bool compress
 * @method static SuperArgumentSignature A_compress() The Signature of: Argument `compress`
 * @property ?string maxSize
 * @method static SuperArgumentSignature A_maxSize() The Signature of: Argument `maxSize`
 * @property bool nullable
 * @method static SuperArgumentSignature A_nullable() The Signature of: Argument `nullable`
 */
class File extends Super
{
    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            'type' => new SuperArgumentSignature(new Enum([
                'values' => ['image', 'document', 'archive', 'pdf', 'word', 'text', 'video'],
                'nullable' => true,
            ]), false, null, false),
            'storeExtension' => new SuperArgumentSignature(new Text(['nullable' => true]), false, null, false),
            'compress' => new SuperArgumentSignature(new Boolean([]), false, true, false),
            'maxSize' => new SuperArgumentSignature(new FileSize(['nullable' => true]), false, null, false),
            'nullable' => new SuperArgumentSignature(new Boolean([]), false, false, false),
        ]);
    }

    protected function _predicate($input, string &$message = null): bool
    {
        if ($this->nullable and $input === null) {
            return true;
        }

        $message = ExpressionService::get('errormessage.not.implemented.yet');
        return false;
    }

    protected function _databaseType(): ?string
    {
        $nullExpression = $this->nullable ? 'NULL' : ' NOT NULL';
        return "VARCHAR(500)$nullExpression";
    }
}