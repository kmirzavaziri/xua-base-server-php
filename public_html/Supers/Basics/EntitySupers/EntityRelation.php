<?php


namespace Supers\Basics\EntitySupers;


use Supers\Basics\Boolean;
use Supers\Basics\Highers\Instance;
use Supers\Basics\Highers\Sequence;
use Supers\Basics\Numerics\Decimal;
use Supers\Basics\Strings\Enum;
use Supers\Basics\Strings\Symbol;
use XUA\Entity;
use XUA\Exceptions\SuperValidationException;
use XUA\Super;
use XUA\Tools\SuperArgumentSignature;

/**
 * @property \XUA\Entity|string relatedEntity
 * @property string relation
 * @property ?string invName
 * @property bool nullable
 * @property bool invNullable
 * @property string definedOn
 */
class EntityRelation extends Super
{
    protected static function _arguments(): array
    {
        return array_merge(parent::_arguments(), [
                'relatedEntity' => new SuperArgumentSignature(new Instance(['of' => Entity::class, 'acceptClass' => true]), true, null, false),
                'relation' => new SuperArgumentSignature(new Enum(['values' => ['II', 'IN', 'NI', 'NN']]), true, null, false),
                'invName' => new SuperArgumentSignature(new Symbol(['nullable' => true]), false, null, false),
                'nullable' => new SuperArgumentSignature(new Boolean([]), false, false, false),
                'invNullable' => new SuperArgumentSignature(new Boolean([]), false, false, false),
                'definedOn' => new SuperArgumentSignature(new Enum(['values' => ['here', 'there']]), false, 'here', false),
            ]);
    }

    protected function _validation(): void
    {
        if ($this->relation[0] == 'N' and $this->invNullable != false) {
            throw new SuperValidationException('Inverse nullable must be false when defining a many-to-? (N?) relation.');
        }

        if ($this->relation[1] == 'N' and $this->nullable != false) {
            throw new SuperValidationException('Nullable must be false when defining a ?-to-many (?N) relation.');
        }
    }

    protected function _predicate($input, string &$message = null) : bool
    {
        if ($this->relation[1] == 'I') {
            if (!(new Instance(['of' => $this->relatedEntity, 'nullable' => true]))->accepts($input, $messages)) {
                $message = implode(' ', $messages);
                return false;
            }
            return true;
        } elseif ($this->relation[1] == 'N') {
            if (!(new Sequence(['type' => new Instance(['of' => $this->relatedEntity])]))->accepts($input, $messages)) {
                $message = implode(' ', $messages);
                return false;
            }
            return true;
        }
        return false;
    }

//    protected function _marshalDatabase($input)
//    {
//        if ($this->relation[1] == 'I') {
//            return $input->id;
//        } elseif ($this->relation[1] == 'N') {
//            return array_map(function ($item) { return $item->id; }, $input);
//        }
//
//        return $input;
//    }
//
//    protected function _unmarshalDatabase($input)
//    {
//        if ($this->relation[1] == 'I') {
//            return new $this->relatedEntity($input);
//        }
//        } elseif ($this->relation[1] == 'N') {
//            # In this case, we expect the input to be the instance of Entity which the field of it is requested
//            if ($this->relation[0] == 'I') {
//                $input
//            } elseif ($this->relation[0] == 'N') {
//                $currentTable = $this->relatedEntity->tableName();
//                $relatingTable = $this->relatedEntity->tableName();
//                $result = Entity::connection()->query("SELECT * FROM " .  . );
//            }
//        }
//    }

    protected function _databaseType(): ?string
    {
        if (($this->relation == 'II' and $this->definedOn == 'here') or $this->relation == 'NI') {
            return (new Decimal([]))->databaseType();
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