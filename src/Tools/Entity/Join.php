<?php

namespace Xua\Core\Tools\Entity;

use Xua\Core\Eves\Entity;
use Xua\Core\Supers\Special\EntityRelation;
use Xua\Core\Exceptions\EntityJoinException;
use Xua\Core\Tools\Signature\Signature;

class Join
{
    const OUTER = 'OUTER';
    const INNER = 'INNER';
    const LEFT = 'LEFT';
    const RIGHT = 'RIGHT';

    public function __construct(
        private string $type,
        private string $leftTableNameAlias,
        private Signature $joiningField,
    ) {
        if (!is_a($this->joiningField->declaration, EntityRelation::class)) {
            throw (new EntityJoinException)->setError($this->joiningField->name, 'Cannot create JOIN for non-relational field.');
        }
    }

    public function rightTableNameAlias() : string
    {
        return $this->leftTableNameAlias . '_' . $this->joiningField->name;
    }

    public function expression() : string
    {
        /** @var EntityRelation $type */
        $type = $this->joiningField->declaration;

        $rightTableName = $type->relatedEntity::table();
        $rightTableNameAlias = $this->rightTableNameAlias();

        if ($type->columnHere) {
            $name = $this->joiningField->name;
            return "$this->type JOIN `$rightTableName` `$rightTableNameAlias` ON `$this->leftTableNameAlias`.`$name` = `$rightTableNameAlias`.`id`";
        } elseif ($type->columnThere) {
            $name = $type->invName;
            return "$this->type JOIN `$rightTableName` `$rightTableNameAlias` ON `$this->leftTableNameAlias`.`id` = `$rightTableNameAlias`.`$name`";
        } else { // $type->hasJunction
            /** @noinspection PhpUndefinedMethodInspection */
            $junctionTableName = $this->joiningField->class::junctionTableName($this->joiningField->name);
            $junctionEntityAlias = $this->leftTableNameAlias . '_j_' . $this->joiningField->name;

            if ($type->definedHere) {
                $here = Entity::JUNCTION_LEFT;
                $there = Entity::JUNCTION_RIGHT;
            } else {
                $here = Entity::JUNCTION_RIGHT;
                $there = Entity::JUNCTION_LEFT;
            }

            return
                "$this->type JOIN $junctionTableName $junctionEntityAlias ON $this->leftTableNameAlias.id = $junctionEntityAlias.$here" . PHP_EOL .
                "$this->type JOIN $rightTableName $rightTableNameAlias ON $junctionEntityAlias.$there = $rightTableNameAlias.id";
        }
    }
}