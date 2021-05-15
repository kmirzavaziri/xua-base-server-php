<?php


namespace Supers\Basics\EntitySupers;


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
 * @property bool nullable
 * @method static SuperArgumentSignature A_nullable() The Signature of: Argument `nullable`
 * @property bool invNullable
 * @method static SuperArgumentSignature A_invNullable() The Signature of: Argument `invNullable`
 * @property string definedOn
 * @method static SuperArgumentSignature A_definedOn() The Signature of: Argument `definedOn`
 */
class EntityRelation extends Super
{
    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
                'relatedEntity' => new SuperArgumentSignature(new Instance(['of' => Entity::class, 'acceptClass' => true]), true, null, false),
                'relation' => new SuperArgumentSignature(new Enum(['values' => ['II', 'IN', 'NI', 'NN']]), true, null, false),
                'invName' => new SuperArgumentSignature(new Symbol(['nullable' => true]), false, null, false),
                'nullable' => new SuperArgumentSignature(new Boolean([]), false, false, false),
                'invNullable' => new SuperArgumentSignature(new Boolean([]), false, false, false),
                'definedOn' => new SuperArgumentSignature(new Enum(['values' => ['here', 'there']]), false, 'here', false),
            ]);
    }

    protected function _validation(SuperValidationException $exception): void
    {
        if ($this->relation[0] == 'N' and $this->invNullable != false) {
            $exception->setError('invNullable', 'Inverse nullable must be false when defining a many-to-? (N?) relation.');
        }

        if ($this->relation[1] == 'N' and $this->nullable != false) {
            $exception->setError('nullable', 'Nullable must be false when defining a ?-to-many (?N) relation.');
        }
    }

    protected function _predicate($input, string &$message = null) : bool
    {
        if ($this->relation[1] == 'I') {
            if (!(new Instance(['of' => $this->relatedEntity, 'nullable' => $this->nullable]))->accepts($input, $messages)) {
                $message = implode(' ', $messages);
                return false;
            }
            if ($input !== null and $input->id === null) {
                if ($this->relation == 'II' and !$this->invNullable) {
                    if (!FlagService::get('force-store-II') and $this->definedOn == 'here') {
                        FlagService::set('force-store-II', true);
                        $input->store();
                        FlagService::unset('force-store-II');
                        return true;
                    } elseif (FlagService::get('force-store-II')) {
                        return true;
                    }
                }
                $message = "$this->relatedEntity with id " . ($input->givenId() === null ? 'NULL' : $input->givenId()) . " does not exist.";
                return false;
            }
            return true;
        } elseif ($this->relation[1] == 'N') {
            if (!(new Sequence(['type' => new Instance(['of' => $this->relatedEntity])]))->accepts($input, $messages)) {
                $message = implode(' ', $messages);
                return false;
            }
            foreach ($input as $item) {
                if ($item->id === null) {
                    $message = "$this->relatedEntity with id " . $item->givenId() . " does not exist.";
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    protected function _marshalDatabase($input) : ?string
    {
        if (($this->relation == 'II' and $this->definedOn == 'here') or $this->relation == 'NI') {
            return $input === null ? null : $input->id;
        }
        return null;
    }

    protected function _databaseType(): ?string
    {
        if (($this->relation == 'II' and $this->definedOn == 'here') or $this->relation == 'NI') {
            return (new Decimal(['unsigned' => true, 'integerLength' => 32, 'base' => 2, 'nullable' => $this->nullable]))->databaseType();
        } else {
            return 'DONT STORE';
        }
    }

    protected function _phpType(): string
    {
        if ($this->relation[1] == 'I') {
            return ($this->nullable ? '?' : '') . "\\$this->relatedEntity";
        } elseif ($this->relation[1] == 'N') {
            return "\\$this->relatedEntity[]";
        }

        return 'mixed';
    }
}