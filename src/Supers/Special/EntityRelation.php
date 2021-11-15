<?php

namespace Xua\Core\Supers\Special;

use Xua\Core\Services\ExpressionService;
use Xua\Core\Services\FlagService;
use Xua\Core\Supers\Boolean;
use Xua\Core\Supers\Highers\Instance;
use Xua\Core\Supers\Highers\Sequence;
use Xua\Core\Supers\Numerics\Decimal;
use Xua\Core\Supers\Strings\Enum;
use Xua\Core\Supers\Strings\Symbol;
use Xua\Core\Supers\Universal;
use Xua\Core\Exceptions\SuperValidationException;
use Xua\Core\Eves\Super;
use Xua\Core\Tools\Signature\Signature;

/**
 * @property mixed relatedEntity
 * @property string relation
 * @property ?string invName
 * @property string definedOn
 * @property bool fromMany
 * @property bool fromOne
 * @property bool toMany
 * @property bool toOne
 * @property bool is11
 * @property bool isN1
 * @property bool is1N
 * @property bool isNN
 * @property bool optional
 * @property bool nullable
 * @property bool required
 * @property bool invOptional
 * @property bool invRequired
 * @property bool hasJunction
 * @property bool definedHere
 * @property bool definedThere
 * @property bool columnHere
 * @property bool columnThere
 */
class EntityRelation extends Super
{
    const relatedEntity = self::class . '::relatedEntity';
    const relation = self::class . '::relation';
    const invName = self::class . '::invName';
    const definedOn = self::class . '::definedOn';
    const fromMany = self::class . '::fromMany';
    const fromOne = self::class . '::fromOne';
    const toMany = self::class . '::toMany';
    const toOne = self::class . '::toOne';
    const is11 = self::class . '::is11';
    const isN1 = self::class . '::isN1';
    const is1N = self::class . '::is1N';
    const isNN = self::class . '::isNN';
    const optional = self::class . '::optional';
    const nullable = self::class . '::nullable';
    const required = self::class . '::required';
    const invOptional = self::class . '::invOptional';
    const invRequired = self::class . '::invRequired';
    const definedHere = self::class . '::definedHere';
    const definedThere = self::class . '::definedThere';
    const columnHere = self::class . '::columnHere';
    const columnThere = self::class . '::columnThere';
    const hasJunction = self::class . '::hasJunction';

    const REL_O11O = 'O11O'; // one-to-one relation;   optional on both sides
    const REL_O11R = 'O11R'; // one-to-one relation;   optional on left and required on right side
    const REL_R11O = 'R11O'; // one-to-one relation;   required on left and optional on right side
    const REL_R11R = 'R11R'; // one-to-one relation;   required on both sides
    const REL_ON1  = 'ON1';  // many-to-one relation;  optional on left side
    const REL_RN1  = 'RN1';  // many-to-one relation;  required on left side
    const REL_1NO  = '1NO';  // one-to-many relation;  optional on right side
    const REL_1NR  = '1NR';  // one-to-many relation;  required on right side
    const REL_NN   = 'NN';   // many-to-many relation;
    const REL_ = [
        self::REL_O11O,
        self::REL_O11R,
        self::REL_R11O,
        self::REL_R11R,
        self::REL_ON1,
        self::REL_RN1,
        self::REL_1NO,
        self::REL_1NR,
        self::REL_NN,
    ];

    const DEFINED_ON_HERE = 'here';
    const DEFINED_ON_THERE = 'there';
    const DEFINED_ON_ = [
        self::DEFINED_ON_HERE,
        self::DEFINED_ON_THERE,
    ];

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(false, static::relatedEntity, true, null,
                new Universal([])
            ),
            Signature::new(false, static::relation, true, null,
                new Enum([Enum::values => self::REL_])
            ),
            Signature::new(false, static::invName, false, null,
                new Symbol([Symbol::nullable => true])
            ),
            Signature::new(false, static::definedOn, false, EntityRelation::DEFINED_ON_HERE,
                new Enum([Enum::values => self::DEFINED_ON_])
            ),
            Signature::new(true, static::fromMany, false, false,new Boolean([])),
            Signature::new(true, static::fromOne, false, false,new Boolean([])),
            Signature::new(true, static::toMany, false, false,new Boolean([])),
            Signature::new(true, static::toOne, false, false,new Boolean([])),
            Signature::new(true, static::is11, false, false,new Boolean([])),
            Signature::new(true, static::isN1, false, false,new Boolean([])),
            Signature::new(true, static::is1N, false, false,new Boolean([])),
            Signature::new(true, static::isNN, false, false,new Boolean([])),
            Signature::new(true, static::optional, false, false,new Boolean([])),
            Signature::new(true, static::nullable, false, false,new Boolean([])),
            Signature::new(true, static::required, false, false,new Boolean([])),
            Signature::new(true, static::invOptional, false, false,new Boolean([])),
            Signature::new(true, static::invRequired, false, false,new Boolean([])),
            Signature::new(true, static::hasJunction, false, false,new Boolean([])),
            Signature::new(true, static::definedHere, false, false,new Boolean([])),
            Signature::new(true, static::definedThere, false, false,new Boolean([])),
            Signature::new(true, static::columnHere, false, false,new Boolean([])),
            Signature::new(true, static::columnThere, false, false,new Boolean([])),
        ]);
    }

    protected function _validation(SuperValidationException $exception): void
    {
        $this->fromMany = in_array($this->relation, [self::REL_NN, self::REL_ON1, self::REL_RN1]);
        $this->fromOne = !$this->fromMany;
        $this->toMany = in_array($this->relation, [self::REL_NN, self::REL_1NO, self::REL_1NR]);
        $this->toOne = !$this->toMany;
        $this->is11 = ($this->fromOne and $this->toOne);
        $this->isN1 = ($this->fromMany and $this->toOne);
        $this->is1N = ($this->fromOne and $this->toMany);
        $this->isNN = ($this->fromMany and $this->toMany);
        $this->optional = in_array($this->relation, [self::REL_O11O, self::REL_O11R, self::REL_ON1]);
        $this->nullable = $this->optional;
        $this->required = !$this->optional;
        $this->invOptional = in_array($this->relation, [self::REL_O11O, self::REL_R11O, self::REL_1NO]);
        $this->invRequired = !$this->invOptional;
        $this->hasJunction = $this->isNN;
        $this->definedHere = ($this->definedOn == self::DEFINED_ON_HERE);
        $this->definedThere = ($this->definedOn == self::DEFINED_ON_THERE);
        $this->columnHere = (($this->is11 and $this->definedHere) or $this->isN1);
        $this->columnThere = (($this->is11 and $this->definedThere) or $this->is1N);
    }

    protected function _predicate($input, null|string|array &$message = null) : bool
    {
        if ($this->toOne) {
            if (!(new Instance([Instance::of => $this->relatedEntity, Instance::nullable => $this->optional]))->explicitlyAccepts($input, $message)) {
                return false;
            }
            if ($input !== null and $input->id === null) {
                if ($this->is11 and $this->invRequired) {
                    if (!FlagService::get('force-store-II') and $this->definedHere) {
                        FlagService::set('force-store-II', true);
                        $input->store();
                        FlagService::unset('force-store-II');
                        return true;
                    } elseif (FlagService::get('force-store-II')) {
                        return true;
                    }
                }
                if ($this->required) {
                    $message = ExpressionService::get('xua.supers.special.entity_relation.error_message.entity_with_id_does_not_exists', [
                        'entity' => ExpressionService::get('table_name.' . $this->relatedEntity::table()),
                        'id' => $input->givenId() === null,
                    ]);
                    return false;
                }
                return true;
            }
            return true;
        } elseif ($this->toMany) {
            if (!(new Sequence([Sequence::type => new Instance([Instance::of => $this->relatedEntity])]))->explicitlyAccepts($input, $message)) {
                return false;
            }
            foreach ($input as $item) {
                if ($item->id === null and $item->givenId() !== 0) {
                    $message = ExpressionService::get('xua.supers.special.entity_relation.error_message.entity_with_id_does_not_exists', [
                        'entity' => ExpressionService::get('table_name.' . $this->relatedEntity::table()),
                        'id' => $input->givenId(),
                    ]);
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    protected function _marshalDatabase($input) : ?string
    {
        if ($this->columnHere) {
            return $input?->id;
        }
        return null;
    }

    protected function _databaseType(): ?string
    {
        if ($this->columnHere) {
            return (new Decimal([Decimal::unsigned => true, Decimal::integerLength => 32, Decimal::base => 2, Decimal::nullable => $this->optional]))->databaseType();
        } else {
            return 'DONT STORE';
        }
    }

    protected function _phpType(): string
    {
        if ($this->toOne) {
            return ($this->optional ? '?' : '') . "\\$this->relatedEntity";
        } else {
            return "\\$this->relatedEntity[]";
        }
    }
}