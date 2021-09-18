<?php


namespace Supers\Basics\EntitySupers;


use Services\XUA\ExpressionService;
use Services\XUA\FlagService;
use Supers\Basics\Boolean;
use Supers\Basics\Highers\Instance;
use Supers\Basics\Highers\Sequence;
use Supers\Basics\Numerics\Decimal;
use Supers\Basics\Strings\Enum;
use Supers\Basics\Strings\Symbol;
use XUA\Entity;
use XUA\Exceptions\SuperValidationException;
use XUA\Super;
use XUA\Tools\Signature\SuperArgumentSignature;

/**
 * @property \XUA\Entity|string relatedEntity
 * @method static SuperArgumentSignature A_relatedEntity() The Signature of: Argument `relatedEntity`
 * @property string relation
 * @method static SuperArgumentSignature A_relation() The Signature of: Argument `relation`
 * @property ?string invName
 * @method static SuperArgumentSignature A_invName() The Signature of: Argument `invName`
 * @property string definedOn
 * @method static SuperArgumentSignature A_definedOn() The Signature of: Argument `definedOn`
 * @property bool fromMany
 * @method static SuperArgumentSignature A_fromMany() The Signature of: Argument `fromMany`
 * @property bool fromOne
 * @method static SuperArgumentSignature A_fromOne() The Signature of: Argument `fromOne`
 * @property bool toMany
 * @method static SuperArgumentSignature A_toMany() The Signature of: Argument `toMany`
 * @property bool toOne
 * @method static SuperArgumentSignature A_toOne() The Signature of: Argument `toOne`
 * @property bool is11
 * @method static SuperArgumentSignature A_is11() The Signature of: Argument `is11`
 * @property bool isN1
 * @method static SuperArgumentSignature A_isN1() The Signature of: Argument `isN1`
 * @property bool is1N
 * @method static SuperArgumentSignature A_is1N() The Signature of: Argument `is1N`
 * @property bool isNN
 * @method static SuperArgumentSignature A_isNN() The Signature of: Argument `isNN`
 * @property bool optional
 * @method static SuperArgumentSignature A_optional() The Signature of: Argument `optional`
 * @property bool nullable
 * @method static SuperArgumentSignature A_nullable() The Signature of: Argument `nullable`
 * @property bool required
 * @method static SuperArgumentSignature A_required() The Signature of: Argument `required`
 * @property bool invOptional
 * @method static SuperArgumentSignature A_invOptional() The Signature of: Argument `invOptional`
 * @property bool invRequired
 * @method static SuperArgumentSignature A_invRequired() The Signature of: Argument `invRequired`
 * @property bool definedHere
 * @method static SuperArgumentSignature A_definedHere() The Signature of: Argument `definedHere`
 * @property bool definedThere
 * @method static SuperArgumentSignature A_definedThere() The Signature of: Argument `definedThere`
 * @property bool columnHere
 * @method static SuperArgumentSignature A_columnHere() The Signature of: Argument `columnHere`
 * @property bool columnThere
 * @method static SuperArgumentSignature A_columnThere() The Signature of: Argument `columnThere`
 * @property bool hasJunction
 * @method static SuperArgumentSignature A_hasJunction() The Signature of: Argument `hasJunction`
 */
class EntityRelation extends Super
{
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
            'relatedEntity' => new SuperArgumentSignature(new Instance(['of' => Entity::class, 'acceptClass' => true]), true, null, false),
            'relation' => new SuperArgumentSignature(new Enum(['values' => self::REL_]), true, null, false),
            'invName' => new SuperArgumentSignature(new Symbol(['nullable' => true]), false, null, false),
            'definedOn' => new SuperArgumentSignature(new Enum(['values' => self::DEFINED_ON_]), false, EntityRelation::DEFINED_ON_HERE, false),
            'fromMany' => new SuperArgumentSignature(new Boolean([]), false, false, true),
            'fromOne' => new SuperArgumentSignature(new Boolean([]), false, false, true),
            'toMany' => new SuperArgumentSignature(new Boolean([]), false, false, true),
            'toOne' => new SuperArgumentSignature(new Boolean([]), false, false, true),
            'is11' => new SuperArgumentSignature(new Boolean([]), false, false, true),
            'isN1' => new SuperArgumentSignature(new Boolean([]), false, false, true),
            'is1N' => new SuperArgumentSignature(new Boolean([]), false, false, true),
            'isNN' => new SuperArgumentSignature(new Boolean([]), false, false, true),
            'optional' => new SuperArgumentSignature(new Boolean([]), false, false, true),
            'nullable' => new SuperArgumentSignature(new Boolean([]), false, false, true),
            'required' => new SuperArgumentSignature(new Boolean([]), false, false, true),
            'invOptional' => new SuperArgumentSignature(new Boolean([]), false, false, true),
            'invRequired' => new SuperArgumentSignature(new Boolean([]), false, false, true),
            'definedHere' => new SuperArgumentSignature(new Boolean([]), false, false, true),
            'definedThere' => new SuperArgumentSignature(new Boolean([]), false, false, true),
            'columnHere' => new SuperArgumentSignature(new Boolean([]), false, false, true),
            'columnThere' => new SuperArgumentSignature(new Boolean([]), false, false, true),
            'hasJunction' => new SuperArgumentSignature(new Boolean([]), false, false, true),
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
        $this->definedHere = ($this->definedOn == self::DEFINED_ON_HERE);
        $this->definedThere = ($this->definedOn == self::DEFINED_ON_THERE);
        $this->columnHere = (($this->is11 and $this->definedHere) or $this->isN1);
        $this->columnThere = (($this->is11 and $this->definedThere) or $this->is1N);
        $this->hasJunction = $this->isNN;
    }

    protected function _predicate($input, null|string|array &$message = null) : bool
    {
        if ($this->toOne) {
            if (!(new Instance(['of' => $this->relatedEntity, 'nullable' => $this->optional]))->explicitlyAccepts($input, $message)) {
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
                $message = ExpressionService::get('errormessage.entity.with.id.does.not.exists', [
                    'entity' => ExpressionService::get('entityclass.' . $this->relatedEntity::table()),
                    'id' => ($input->givenId() === null ? 'NULL' : $input->givenId()),
                ]);
                return false;
            }
            return true;
        } elseif ($this->toMany) {
            if (!(new Sequence(['type' => new Instance(['of' => $this->relatedEntity])]))->explicitlyAccepts($input, $message)) {
                return false;
            }
            foreach ($input as $item) {
                if ($item->id === null and $item->givenId() !== 0) {
                    $message = "$this->relatedEntity with id " . $item->givenId() . ' does not exist.';
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
            return (new Decimal(['unsigned' => true, 'integerLength' => 32, 'base' => 2, 'nullable' => $this->optional]))->databaseType();
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