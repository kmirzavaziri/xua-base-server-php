<?php


namespace Supers\Basics\Highers;



use Services\XUA\DateTimeInstance;
use Services\XUA\LocaleLanguage;
use Supers\Basics\Boolean;
use Supers\Basics\Strings\Text;
use XUA\Tools\Signature\SuperArgumentSignature;

/**
 * @property string of
 * @method static SuperArgumentSignature A_of() The Signature of: Argument `of`
 * @property bool strict
 * @method static SuperArgumentSignature A_strict() The Signature of: Argument `strict`
 * @property bool acceptClass
 * @method static SuperArgumentSignature A_acceptClass() The Signature of: Argument `acceptClass`
 * @property bool nullable
 * @method static SuperArgumentSignature A_nullable() The Signature of: Argument `nullable`
 */
class DateTime extends Instance
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
        return $input->formatLocal('Y-m-d H:i:s');
    }

    protected function _unmarshal($input): mixed
    {
        return $input ? (DateTimeInstance::fromLocalYmdHis($input) ?? $input) : $input;
    }

    protected function _marshalDatabase($input): mixed
    {
        /** @var ?DateTimeInstance $input */
        return $input ? $input->formatGregorian('Y-m-d H:i:s', LocaleLanguage::LANG_EN) : $input;
    }

    protected function _unmarshalDatabase($input): mixed
    {
        return $input ? (DateTimeInstance::fromGregorianYmdHis($input) ?? $input) : $input;
    }

    protected function _databaseType(): ?string
    {
        $nullExpression = $this->nullable ? 'NULL' : ' NOT NULL';
        return "DATETIME$nullExpression";
    }
}