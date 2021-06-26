<?php


namespace Supers\Basics\Highers;



use Services\XUA\ExpressionService;
use Supers\Basics\Boolean;
use XUA\Super;
use XUA\Tools\Signature\SuperArgumentSignature;

/**
 * @property bool nullable
 * @method static SuperArgumentSignature A_nullable() The Signature of: Argument `nullable`
 */
class Date extends Super
{
    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
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
        return "DATE$nullExpression";
    }
}