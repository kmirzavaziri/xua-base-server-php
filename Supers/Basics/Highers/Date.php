<?php


namespace Supers\Basics\Highers;



use Services\XUA\DateTimeInstance;
use Services\XUA\ExpressionService;
use Services\XUA\LocaleLanguage;
use Supers\Basics\Boolean;
use Supers\Basics\Strings\Text;
use XUA\Super;
use XUA\Tools\Signature\SuperArgumentSignature;

/**
 * @property bool nullable
 * @method static SuperArgumentSignature A_nullable() The Signature of: Argument `nullable`
 */
class Date extends Instance
{
    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            'of' => new SuperArgumentSignature(new Text([]), false, DateTimeInstance::class, true),
            'strict' => new SuperArgumentSignature(new Boolean([]), false, false, true),
            'acceptClass' => new SuperArgumentSignature(new Boolean([]), false, false, true),
        ]);
    }

    protected function _marshal($input): mixed
    {
        /** @var DateTimeInstance $input */
        return $input ? $input->formatLocal('Y-m-d') : $input;
    }

    protected function _unmarshal($input): mixed
    {
        return $input ? (DateTimeInstance::fromLocalYmd($input) ?? $input) : $input;
    }

    protected function _marshalDatabase($input): mixed
    {
        /** @var ?DateTimeInstance $input */
        return $input ? $input->formatGregorian('Y-m-d', LocaleLanguage::LANG_EN) : $input;
    }

    protected function _unmarshalDatabase($input): mixed
    {
        return $input ? (DateTimeInstance::fromGregorianYmd($input) ?? $input) : $input;
    }

    protected function _databaseType(): ?string
    {
        $nullExpression = $this->nullable ? 'NULL' : ' NOT NULL';
        return "DATE$nullExpression";
    }
}