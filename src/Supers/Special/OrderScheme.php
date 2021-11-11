<?php

namespace Xua\Core\Supers\Special;

use Xua\Core\Eves\Super;
use Xua\Core\Exceptions\SuperValidationException;
use Xua\Core\Supers\Boolean;
use Xua\Core\Supers\Highers\Sequence;
use Xua\Core\Supers\Highers\StructuredMap;
use Xua\Core\Supers\Strings\Enum;
use Xua\Core\Supers\Strings\Symbol;
use Xua\Core\Supers\Strings\Text;
use Xua\Core\Tools\Signature\Signature;

/**
 * @property array fields
 * @property bool unique
 * @property ?string name
 */
class OrderScheme extends Super
{
    const fields = self::class . '::fields';
    const unique = self::class . '::unique';
    const name = self::class . '::name';

    const field = 'field';
    const direction = 'direction';

    const DIRECTION_ASC = 'ASC';
    const DIRECTION_DESC = 'DESC';
    const DIRECTION_ = [
        self::DIRECTION_ASC,
        self::DIRECTION_DESC
    ];

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(false, static::fields, true, null,
                new Sequence([
                    Sequence::nullable => false,
                    Sequence::type => new StructuredMap([
                        StructuredMap::nullable => false,
                        StructuredMap::structure => [
                            self::direction => new Enum([Enum::values => self::DIRECTION_]),
                            self::field => new Symbol([Symbol::nullable => false]),
                        ]
                    ])
                ])
            ),
            Signature::new(false, static::unique, false, false, new Boolean([])),
            Signature::new(false, static::name, false, null, new Text([Text::nullable => true])),
        ]);
    }

    protected function _validation(SuperValidationException $exception): void
    {
        if (!$this->name) {
            $fieldExpression = '';
            foreach ($this->fields as $field) {
                $fieldExpression .= '_' . $field[self::field] . '_' . strtolower($field[self::direction]);
            }
            $this->name = $fieldExpression . ($this->unique ? '_unique' : '') . '_index';
        }
    }

    protected function _predicate($input, null|string|array &$message = null): bool
    {
        return false;
    }

    protected function _databaseType(): ?string
    {
        return 'DONT STORE';
    }

    protected function _phpType(): string
    {
        return 'null';
    }
}