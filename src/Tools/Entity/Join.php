<?php

namespace Xua\Core\Tools\Entity;

use Xua\Core\Supers\EntitySupers\EntityRelation;
use Xua\Core\Exceptions\EntityJoinException;
use Xua\Core\Tools\Signature\Signature;

class Join
{
    const OUTER = 'OUTER';
    const INNER = 'INNER';
    const LEFT = 'LEFT';
    const RIGHT = 'RIGHT';

    /**
     * @throws EntityJoinException
     */
    public function __construct(
        private string $type,
        private string $leftTableNameAlias,
        private Signature $joiningField,
    ) {
        if (!is_a($this->joiningField->type, EntityRelation::class)) {
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
        $type = $this->joiningField->type;

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

            /** @noinspection PhpUndefinedMethodInspection */
            $leftTableName = $this->joiningField->class::table();

            return
                "$this->type JOIN $junctionTableName $junctionEntityAlias ON $this->leftTableNameAlias.id = $junctionEntityAlias.$leftTableName" . PHP_EOL .
                "$this->type JOIN $rightTableName $rightTableNameAlias ON $junctionEntityAlias.$rightTableName = $rightTableNameAlias.id";
        }
    }
}