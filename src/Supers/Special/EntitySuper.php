<?php

namespace Xua\Core\Supers\Special;

use Xua\Core\Eves\Entity;
use Xua\Core\Supers\Boolean;
use Xua\Core\Supers\Highers\Instance;
use Xua\Core\Supers\Strings\Enum;
use Xua\Core\Supers\Strings\Text;
use Xua\Core\Exceptions\SuperValidationException;
use Xua\Core\Eves\Super;
use Xua\Core\Tools\Entity\CF;
use Xua\Core\Tools\Entity\Condition;
use Xua\Core\Tools\Signature\Signature;

/**
 * @property bool nullable
 * @property \Xua\Core\Eves\Entity|string relatedEntity
 * @property string representativeField
 * @property string type
 */
class EntitySuper extends Super
{
    const nullable = self::class . '::nullable';
    const relatedEntity = self::class . '::relatedEntity';
    const representativeField = self::class . '::representativeField';
    const type = self::class . '::type';

    const TYPE_ONE = 'one';
    const TYPE_MANY = 'many';
    const TYPE_ = [
        self::TYPE_ONE,
        self::TYPE_MANY,
    ];

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(false, static::nullable, false, false,
                new Boolean([])
            ),
            Signature::new(false, static::relatedEntity, true, null,
                new Instance([
                    Instance::of => Entity::class,
                    Instance::nullable => false,
                    Instance::acceptClass => true,
                    Instance::acceptObject => false,
                    Instance::strict => false,
                ])
            ),
            Signature::new(false, static::representativeField, false, null,
                new Text([])
            ),
            Signature::new(false, static::type, true, null,
                new Enum([Enum::values => self::TYPE_])
            ),
        ]);
    }

    protected function _validation(SuperValidationException $exception): void
    {
        if (Signature::_($this->representativeField)?->class != $this->relatedEntity) {
            $exception->setError(Signature::_(static::representativeField)->name, 'representativeField must be a field in relatedEntity.'); // @TODO message from dict
        }
    }

    protected function _predicate($input, null|string|array &$message = null) : bool
    {
        if ($this->nullable and $input === null) {
            return true;
        }

        if (is_a($input, $this->relatedEntity, true)) {
            $message = "Class $input is not a subclass of class $this->relatedEntity."; // @TODO message from dict
            return false;
        }

        return true;
    }

    protected function _marshal(mixed $input) : mixed
    {
        return $input?->{Signature::_($this->representativeField)->name};
    }

    protected function _unmarshal(mixed $input) : mixed
    {
        return $this->relatedEntity::getOne(Condition::leaf(CF::_($this->representativeField), Condition::EQ, $input));
    }

    protected function _marshalDatabase(mixed $input) : mixed
    {
        return $this->_marshal($input);
    }

    protected function _unmarshalDatabase(mixed $input) : mixed
    {
        return $this->_unmarshal($input);
    }

    protected function _databaseType(): ?string
    {
        return 'DONT STORE';
    }

    protected function _phpType(): string
    {
        return ($this->nullable ? '' : '?') .
            ($this->representativeField !== null
                ? Signature::_($this->representativeField)->declaration->phpType()
                : "\\$this->relatedEntity"
            );
    }
}