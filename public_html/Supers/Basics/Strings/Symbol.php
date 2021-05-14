<?php


namespace Supers\Basics\Strings;


use XUA\Tools\Signature\SuperArgumentSignature;

/**
 * @property ?int minLength
 * @property ?int maxLength
 * @property bool nullable
 * @property string pattern
 */
class Symbol extends Regex
{
    protected static function _arguments(): array
    {
        return array_merge(parent::_arguments(), [
            'pattern' => new SuperArgumentSignature(new Text([]), false, '/^[a-zA-Z_][a-zA-Z_0-9]*$/', true)
        ]);
    }
}