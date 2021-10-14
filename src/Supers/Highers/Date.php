<?php


namespace Xua\Core\Supers\Highers;



use Xua\Core\Services\DateTimeInstance;
use Xua\Core\Services\ExpressionService;
use Xua\Core\Services\LocaleLanguage;
use Xua\Core\Supers\Boolean;
use Xua\Core\Supers\Strings\Text;
use Xua\Core\Eves\Super;
use Xua\Core\Tools\Signature\SuperArgumentSignature;

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

    protected function _predicate($input, array|string|null &$message = null): bool
    {
        $message = ExpressionService::get('errormessage.invalid.date', ['date' => is_string($input) ? $input : xua_var_dump($input)]);
        return parent::_predicate($input);
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