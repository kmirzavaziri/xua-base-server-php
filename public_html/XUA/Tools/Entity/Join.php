<?php


namespace XUA\Tools\Entity;


use Exception;
use Supers\Basics\EntitySupers\EntityRelation;
use XUA\Tools\Signature\EntityFieldSignature;

class Join
{
    const OUTER = 'OUTER';
    const INNER = 'INNER';
    const LEFT = 'LEFT';
    const RIGHT = 'RIGHT';
    public function __construct(
        private string $type,
        private string $leftTableNameAlias,
        private EntityFieldSignature $joiningField,
    ) {
        if (!is_a($this->joiningField->type, EntityRelation::class)) {
            throw new Exception('Cannot create JOIN for non-relational field.');
        }
    }

    public function rightTableNameAlias() : string
    {
        return $this->leftTableNameAlias . '_' . $this->joiningField->name;
    }

    public function expression() : string
    {
        /** @var EntityRelation $type */
        $type = $this->joiningField->type;

        $rightTableName = $type->relatedEntity::table();
        $rightTableNameAlias = $this->rightTableNameAlias();

        if (($type->relation == 'II' and $type->definedOn == 'here') or $type->relation == 'IN') {
            $name = $this->joiningField->name;
            return "$this->type JOIN $rightTableName $rightTableNameAlias ON $this->leftTableNameAlias.$name = $rightTableNameAlias.id";
        } elseif (($type->relation == 'II' and $type->definedOn == 'there') or $type->relation == 'NI') {
            $name = $type->invName;
            return "$this->type JOIN $rightTableName $rightTableNameAlias ON $this->leftTableNameAlias.id = $rightTableNameAlias.$name";
        } else { // NN
            $junctionTableName = $this->joiningField->entity::junctionTableName($this->joiningField->name);
            $junctionEntityAlias = $this->leftTableNameAlias . '_j_' . $this->joiningField->name;

            $leftTableName = $this->joiningField->entity::table();

            return
                "$this->type JOIN $junctionTableName $junctionEntityAlias ON $this->leftTableNameAlias.id = $junctionEntityAlias.$leftTableName" . PHP_EOL .
                "$this->type JOIN $rightTableName $rightTableNameAlias ON $junctionEntityAlias.$rightTableName = $rightTableNameAlias.id";
        }
    }
}