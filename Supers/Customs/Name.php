<?php


namespace Supers\Customs;


use Services\XUA\ExpressionService;
use Services\XUA\LocaleLanguage;
use Supers\Basics\Strings\Enum;
use Supers\Basics\Strings\Text;
use XUA\Tools\Signature\SuperArgumentSignature;

/**
 * @property ?int minLength
 * @method static SuperArgumentSignature A_minLength() The Signature of: Argument `minLength`
 * @property ?int maxLength
 * @method static SuperArgumentSignature A_maxLength() The Signature of: Argument `maxLength`
 * @property bool nullable
 * @method static SuperArgumentSignature A_nullable() The Signature of: Argument `nullable`
 * @property string language
 * @method static SuperArgumentSignature A_language() The Signature of: Argument `language`
 */
class Name extends Text
{
    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            'language' => new SuperArgumentSignature(new Enum(['values' => [LocaleLanguage::LANG_FA, LocaleLanguage::LANG_EN]]), true, null, false),
        ]);
    }

    protected function _predicate($input, string &$message = null): bool
    {
        if (!parent::_predicate($input, $message)) {
            return false;
        }

        if ($this->nullable and $input === null) {
            return true;
        }

        $validCharacters = preg_split('//u', match ($this->language) {
            LocaleLanguage::LANG_FA => 'ابپتثجچحخدذرزژسشصضطظعغفقکگلمنوهی ءآأؤئًٌٍَُِّْٰ',
            LocaleLanguage::LANG_EN => 'abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ'
        }, -1, PREG_SPLIT_NO_EMPTY);
        $validCharactersMap = [];
        foreach ($validCharacters as $validCharacter) {
            $validCharactersMap[$validCharacter] = true;
        }

        $characters = preg_split('//u', $input, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($characters as $character) {
            if (!($validCharactersMap[$character] ?? false)) {
                $message = ExpressionService::get('errormessage.invalid.character.character', ['character' => $character]);
                return false;
            }
        }
        return true;
    }

    protected function _marshal(mixed $input): mixed
    {
        // @TODO replace ي with ی and k as well
        return parent::_marshal($input);
    }
}