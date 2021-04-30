<?php

namespace XUA;

use PDO;
use Supers\Basics\EntitySupers\DatabaseVirtualField;
use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\EntitySupers\PhpVirtualField;
use Supers\Basics\Numerics\Decimal;
use XUA\Exceptions\EntityException;
use XUA\Exceptions\EntityFieldException;
use XUA\Tools\Column;
use XUA\Tools\Condition;
use XUA\Tools\EntityFieldSignature;
use XUA\Tools\Order;
use XUA\Tools\Pager;
use XUA\Tools\TableScheme;
use XUA\Tools\Visibility;

/**
 * @property int id
 */
abstract class Entity extends XUA
{
    # Connection Information & Procedures
    private const CONNECTION_DSN = "mysql:host=db;dbname=myfarm";
    private const CONNECTION_USERNAME = "root";
    private const CONNECTION_PASSWORD = "root";
    private static ?PDO $connection = null;

    final public static function connection() : ?PDO
    {
        return self::$connection;
    }

    # Magics
    private static array $_x_table = [];

    private static array $_x_structure = [];
    private array $_x_properties = [];

    private bool $_x_toDelete = false;
    private array $_x_fetched = [];
    private array $_x_stored = [];

    /* DONE */ final public static function _init() : void
    {
        $tableNameTemp = explode("\\", static::class);
        array_shift($tableNameTemp);
        self::$_x_table[static::class] = implode('', $tableNameTemp);

        self::$_x_structure[static::class] = static::fields();

        if (!self::$connection) {
            self::$connection = new PDO(self::CONNECTION_DSN, self::CONNECTION_USERNAME, self::CONNECTION_PASSWORD);
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

    /* DONE */ final public function __construct(?int $id = null)
    {
        $this->initialize();

        $exists = false;
        if ($id) {
            $statement = self::$connection->prepare("SELECT EXISTS (SELECT * FROM " . static::table() . " WHERE id = ?) e");
            $statement->execute([$id]);
            $exists = $statement->fetch()['e'];
        }
        if ($exists) {
            $this->_x_properties['id'] = $id;
            $this->_x_fetched['id'] = true;
            $this->_x_stored['id'] = true;
        }
    }

    /* DONE */ function __get($key)
    {
        if (! isset(static::structure()[$key])) {
            throw new EntityFieldException("$key is not in (" . implode(', ', array_keys(static::structure())) . ")");
        }

        if (!$this->_x_fetched[$key]) {
            $this->_x_fetch($key);
        }

        return $this->_x_properties[$key];
    }

    function __set($key, $value) : void
    {
        if (! isset(static::structure()[$key])) {
            throw new EntityFieldException("'$key' is not in " . implode(', ', array_keys(static::structure())) . ".");
        }

        if ($key == 'id') {
            throw new EntityFieldException("Cannot set field 'id' of an entity.");
        }

        EntityFieldSignature::processField(static::structure()[$key], $value);

        if ($this->_x_properties[$key] != $value) {
            $this->_x_properties[$key] = $value;
            $this->_x_stored[$key] = false;
        };
    }

    /* DONE */ public function __debugInfo(): array
    {
//        $this->_x_fetch();
        return $this->_x_properties;
    }

    /* DONE */ final public static function structure() : array {
        return self::$_x_structure[static::class];
    }

    /* DONE */ final public function properties() : array {
        return $this->_x_properties;
    }

    /* DONE */ final public static function table() : string
    {
        return self::$_x_table[static::class];
    }

    # Overridable Methods
    /* DONE */ protected static function _fields() : array
    {
        return [
            'id' => new EntityFieldSignature(static::class, 'id', new Decimal(['unsigned' => true]), null),
        ];
    }

    /* DONE */ protected static function _indexes() : array
    {
        return [
            ['*', 'id'],
        ];
    }

    /* DONE */ protected function _validation() : void
    {
        # Empty by default
    }

    /* DONE */ protected function _initialize(string $caller) : void
    {
        $this->_x_initialize();
    }

    /* DONE */ protected static function _getOne(Condition $condition, Order $order, string $caller) : Entity
    {
        return static::_x_getOne($condition, $order);
    }

    /* DONE */ protected function _store(string $caller) : Entity
    {
        return $this->_x_store();
    }

    /* DONE */ protected function _markToDelete(string $caller) : Entity
    {
        return $this->_x_markToDelete();
    }

    /* DONE */ protected function _unmarkToDelete(string $caller) : Entity
    {
        return $this->_x_unmarkToDelete();
    }

    /* DONE */ protected static function _getMany(Condition $condition, Order $order, Pager $pager, string $caller) : array
    {
        return static::_x_getMany($condition, $order, $pager);
    }

    /* DONE */ protected static function _setMany(array $changes, Condition $condition, Order $order, Pager $pager, string $caller) : void
    {
        static::_x_setMany($changes, $condition, $order, $pager);
    }

    /* DONE */ protected static function _deleteMany(Condition $condition, Order $order, Pager $pager, string $caller) : void
    {
        static::_x_deleteMany($condition, $order, $pager);
    }

    # Overridable Method Wrappers
    /* DONE */ private static function fields() : array
    {
        return static::_fields();
    }

    /* DONE */ final public static function indexes() : array
    {
        return static::_indexes();
    }

    /* DONE */ private function validation() : void
    {
        # @TODO validate by signatures
        $this->_validation();
    }

    /* DONE */ private function initialize(string $caller = Visibility::CALLER_PHP) : void
    {
        $this->_initialize($caller);
    }

    /* DONE */ final public static function getOne(?Condition $condition = null, ?Order $order = null, string $caller = Visibility::CALLER_PHP) : Entity
    {
        if ($condition === null) {
            $condition = Condition::trueLeaf();
        }
        if ($order === null) {
            $order = Order::noOrder();
        }
        return static::_getOne($condition, $order, $caller);
    }

    /* DONE */ final public function store(string $caller = Visibility::CALLER_PHP) : Entity
    {
        return $this->_store($caller);
    }

    /* DONE */ final public function markToDelete(string $caller = Visibility::CALLER_PHP) : Entity
    {
        return $this->_markToDelete($caller);
    }

    /* DONE */ final public function unmarkToDelete(string $caller = Visibility::CALLER_PHP) : Entity
    {
        return $this->_unmarkToDelete($caller);
    }

    /* DONE */ final public static function getMany(?Condition $condition = null, ?Order $order = null, ?Pager $pager = null, string $caller = Visibility::CALLER_PHP) : array
    {
        if ($condition === null) {
            $condition = Condition::trueLeaf();
        }
        if ($order === null) {
            $order = Order::noOrder();
        }
        if ($pager === null) {
            $pager = Pager::unlimited();
        }
        return static::_getMany($condition, $order, $pager, $caller);
    }

    /* DONE */ final public static function setMany(array $changes, ?Condition $condition = null, ?Order $order = null, ?Pager $pager = null, string $caller = Visibility::CALLER_PHP) : void
    {
        if ($condition === null) {
            $condition = Condition::trueLeaf();
        }
        if ($order === null) {
            $order = Order::noOrder();
        }
        if ($pager === null) {
            $pager = Pager::unlimited();
        }
        static::_setMany($changes, $condition, $order, $pager, $caller);
    }

    /* DONE */ final public static function deleteMany(?Condition $condition = null, ?Order $order = null, ?Pager $pager = null, string $caller = Visibility::CALLER_PHP) : void
    {
        if ($condition === null) {
            $condition = Condition::trueLeaf();
        }
        if ($order === null) {
            $order = Order::noOrder();
        }
        if ($pager === null) {
            $pager = Pager::unlimited();
        }
        static::_deleteMany($condition, $order, $pager, $caller);
    }

    # Predefined Methods (to wrap in overridable methods)
    /* DONE */ final protected function _x_initialize() : void {
        foreach (static::structure() as $key => $signature) {
            $this->_x_properties[$key] = $signature->default;
            $this->_x_fetched[$key] = false;
            $this->_x_stored[$key] = false;
        }
    }

    /* DONE */ final protected static function _x_getOne(Condition $condition, Order $order) : Entity
    {
        return static::_x_getMany($condition, $order, new Pager(1, 0))[0] ?? new static();
    }

    final protected function _x_store() : Entity
    {
        if ($this->_x_stored) {
            return $this;
        }

        if (!$this->_x_toDelete and !$this->id) {
            $this->_x_insert();
        } elseif (!$this->_x_toDelete and $this->id) {
            $this->_x_update();
        } elseif ($this->_x_toDelete and $this->id) {
            $this->_x_delete();
        }

        $this->_x_stored = true;
        return $this;
    }

    /* DONE */ final protected function _x_markToDelete() : Entity
    {
        if (!$this->_x_toDelete) {
            $this->_x_toDelete = true;
            $this->_x_stored = false;
        }
        return $this;
    }

    /* DONE */ final protected function _x_unmarkToDelete() : Entity
    {
        if ($this->_x_toDelete) {
            $this->_x_toDelete = false;
            $this->_x_stored = false;
        }
        return $this;
    }

    /* DONE */ final protected static function _x_getMany(Condition $condition, Order $order, Pager $pager) : array
    {
        [$columnsExpression, $joinsExpression, $keys] = self::columnsExpression();
        $statement = self::connection()->prepare("SELECT $columnsExpression FROM " . static::table() . " $joinsExpression WHERE $condition->template " . $order->render() . "");
        $statement->execute($condition->parameters);
        $rawArrays = $statement->fetchAll(PDO::FETCH_NUM);
        $arrays = [];
        foreach ($rawArrays as $item => $rawArray) {
            $arrays[$item] = [];
            foreach ($keys as $i => $key) {
                $arrays[$item][$key] = $rawArray[$i];
            }
        }
        $entities = [];
        foreach ($arrays as $array) {
            $entities[] = (new static())->fromArray($array);
        }

        return $entities;
    }

    final protected static function _x_setMany(array $changes, Condition $condition, Order $order, Pager $pager) : void
    {
        # @TODO implement
    }

    final protected static function _x_deleteMany(Condition $condition, Order $order, Pager $pager) : void
    {
        # @TODO implement
    }

    # Predefined Methods (Array-Entity Conversations)
    final protected function fromArray (array $array) : Entity {
        foreach ($array as $key => $value) {
            /** @var EntityFieldSignature $signature */
            $signature = static::structure()[$key];
            if (is_a($signature->type, EntityRelation::class)) {
                if ($signature->type->relation[1] == 'I') {
                    $result = new $signature->type->relatedEntity($value);
                    if ($result->id) {
                        if ($signature->type->relation[0] == 'I') {
                            $result->_x_properties[$signature->type->invName] = $this;
                            $result->_x_fetched[$signature->type->invName] = true;
                            $result->_x_stored[$signature->type->invName] = true;
                        }
                    } else {
                        $result = null;
                    }
                } elseif ($signature->type->relation[1] == 'N') {
                    $result = [];
                    if ($value) {
                        foreach ($value as $id) {
                            $tmp = new $signature->type->relatedEntity($id);
                            if ($tmp->id) {
                                if ($signature->type->relation[0] == 'I') {
                                    $tmp->_x_properties[$signature->type->invName] = $this;
                                    $tmp->_x_fetched[$signature->type->invName] = true;
                                    $tmp->_x_stored[$signature->type->invName] = true;
                                }
                                $result[] = $tmp;
                            }
                        }
                    }
                }
            } elseif (is_a($signature->type, PhpVirtualField::class)) {

            } elseif (is_a($signature->type, DatabaseVirtualField::class)) {

            } else {
                $result = $signature->type->unmarshalDatabase($value);
            }
            $this->_x_properties[$key] = $result;
            $this->_x_fetched[$key] = true;
            $this->_x_stored[$key] = true;
        }

        return $this;
    }

    final protected function toArray () : array {
        $array = [];
        foreach (static::structure() as $key => $signature) {
            /** @var EntityFieldSignature $signature */
            if ($signature->type->databaseType() != 'DONT STORE' and $key != 'id') {
                $array[$key] = $signature->type->marshalDatabase($this->_x_properties[$key]);
            }
        }
        return $array;
    }

    # Predefined Methods (low-level direct mysql communicator)
    private function _x_fetch(?string $fieldName = 'id') : void
    {
        if (!($this->_x_properties['id'] ?? false)) {
            return;
        }

        $signature = static::structure()[$fieldName];
        $array = [];

        if (
            is_a($signature->type, EntityRelation::class) and
            $signature->type->relation[1] == 'N'
        ) {
            if ($signature->type->relation == 'IN') {
                $statement = self::connection()->prepare("SELECT id FROM " . $signature->type->relatedEntity::table() . " WHERE " . $signature->type->invName . " = ?");
                $statement->execute([$this->_x_properties['id']]);
                $rawArray = $statement->fetchAll(PDO::FETCH_NUM);
                if ($rawArray) {
                    $array[$fieldName] = [];
                    foreach ($rawArray as $item) {
                        $array[$fieldName][] = $item[0];
                    }
                }
            } elseif ($signature->type->relation == 'NN') {
                $statement = self::connection()->prepare("SELECT " . $signature->type->relatedEntity::table() . " FROM " . self::junctionTableName($fieldName) . " WHERE " . static::table() . " = ?");
                $statement->execute([$this->_x_properties['id']]);
                $rawArray = $statement->fetchAll(PDO::FETCH_NUM);
                if ($rawArray) {
                    $array[$fieldName] = [];
                    foreach ($rawArray as $item) {
                        $array[$fieldName][] = $item[0];
                    }
                }
            }
        } elseif (is_a($signature->type, PhpVirtualField::class)) {

        } else {
            [$columnsExpression, $joinsExpression, $keys] = self::columnsExpression($this);
            if ($columnsExpression) {
                $statement = self::connection()->prepare("SELECT $columnsExpression FROM " . static::table() . " $joinsExpression WHERE " . static::structure()['id']->name . " = ? LIMIT 1");
                $statement->execute([$this->_x_properties['id']]);
                $rawArray = $statement->fetch(PDO::FETCH_NUM);
                if ($rawArray) {
                    foreach ($keys as $i => $key) {
                        $array[$key] = $rawArray[$i];
                    }
                }
            }
        }

        $this->fromArray($array);
    }

    private function _x_insert() : void {
        $this->validation();

        $array = $this->toArray();
        $keys = implode(', ', array_keys($array));
        $questionMarks = implode(', ', array_fill(0, count($array), '?'));
        $values = array_values($array);
        $statement = self::connection()->prepare("INSERT INTO " . static::table() . " ($keys) VALUES ($questionMarks)");
        $statement->execute($values);
    }

    private function _x_update() : void {
//        self::connection()->query("DELETE FROM $table $condition");
    }

    private function _x_delete() : void {
        foreach (static::structure() as $key => $signature) {
            /** @var EntityFieldSignature $signature */
            if (is_a($signature->type, EntityRelation::class) and $signature->type->relation[0] == 'I' and !$signature->type->invNullable and $this->$key) {
                throw new EntityException("Cannot delete " . static::table() . " because there exists a $key but the inverse nullable is false.");
            }
        }

        foreach (static::structure() as $key => $signature) {
            /** @var EntityFieldSignature $signature */
            if (is_a($signature->type, EntityRelation::class)) {
                if (
                    ($signature->type->relation == 'II' and $signature->type->definedOn == 'there') or
                    $signature->type->relation == 'IN'
                ) {
                    $this->$key->{$signature->type->invName} = null;
                    $this->$key->store();
                } elseif ($signature->type->relation = 'NN') {
                    $this->$key = [];
                    $this->store();
                }
            }
        }

        $statement = self::connection()->prepare("DELETE FROM " . static::table() . " WHERE id = ? LIMIT 1");
        $statement->execute([$this->id]);
    }

    # Predefined Methods (helpers)
    /* TODO: indexes */ final public static function alter() : string
    {
        $signatures = static::structure();
        unset($signatures['id']);

        $tables = [];

        $idColumn = Column::fromQuery("id " . static::structure()['id']->type->databaseType() . " NOT NULL PRIMARY KEY AUTO_INCREMENT");

        $columns = ['id' => $idColumn];
        foreach ($signatures as $key => $signature) {
            /** @var EntityFieldSignature $signature */
            if ($signature->type->databaseType() != 'DONT STORE') {
                $columns[$key] = Column::fromQuery("$key {$signature->type->databaseType()}");
            }
            if (
                is_a($signature->type, EntityRelation::class) and
                $signature->type->relation == 'NN' and
                $signature->type->definedOn == 'here'
            ) {
                $tmpColumns[$key] = Column::fromQuery("$key {$signature->type->databaseType()}");
                $tables[] = new TableScheme('_' . static::table() . '_' . $key, [
//                    'id' => $idColumn,
                    static::table() => Column::fromQuery(static::table() . ' ' . $signature->type->relatedEntity::structure()['id']->type->databaseType() . " NOT NULL"),
                    $signature->type->relatedEntity::table() => Column::fromQuery($signature->type->relatedEntity::table() . ' ' . $signature->type->relatedEntity::structure()['id']->type->databaseType() . " NOT NULL"),
                ]);
            }

        }

        $tables[] = (new TableScheme(static::table(), $columns));

        $result = '';
        foreach ($tables as $table) {
            $result .= $table->alter();
        }

        return $result;
    }

    /* DONE */ private static function joinExpression(EntityFieldSignature $signature) : string
    {
        if ($signature->type->relation == 'II' and $signature->type->definedOn == 'there') {
            $name = $signature->rel->entity();
            $idName = static::structure()['id']->name;
            $thereEntity = $signature->type->relatedEntity::table();
            $thereName = $signature->type->invName;
            return "LEFT JOIN $thereEntity $name ON $idName = $name.$thereName";
        }

        return '';
    }

    /* DONE */ private static function columnsExpression(?Entity $entity = null) : array
    {
        $columnExpressions = [];
        $joiningFields = [];
        $keys = [];
        foreach (static::structure() as $key => $signature) {
            /** @var EntityFieldSignature $signature */
            if ($entity and $entity->_x_fetched[$key]) {
                continue;
            }
            if (is_a($signature->type, EntityRelation::class)) {
                if (($signature->type->relation == 'II' and $signature->type->definedOn == 'here') or $signature->type->relation == 'NI') {
                    $expression =  $signature->name;
                } elseif ($signature->type->relation == 'II' and $signature->type->definedOn == 'there') {
                    $expression = $signature->rel->id;
                    $joiningFields[] = $signature;
                } else {
                    continue;
                }
            } elseif (is_a($signature->type, PhpVirtualField::class)) {
                continue;
            } elseif (is_a($signature->type, DatabaseVirtualField::class)) {

            } else {
                $expression =  $signature->name;
            }

            $columnExpressions[] = $expression;
            $keys[] = $key;
        }

        $joinExpressions = [];
        foreach ($joiningFields as $joiningField) {
            $joinExpressions[] = self::joinExpression($joiningField);
        }

        $columnsExpression = implode(', ', $columnExpressions);
        $joinsExpression = implode(PHP_EOL, $joinExpressions);

        return [$columnsExpression, $joinsExpression, $keys];
    }

    /* DONE */ private static function junctionTableName (string $fieldName)
    {
        $signature = static::structure()[$fieldName];
        return $signature->type->definedOn == 'here'
            ? '_' . static::table() . '_' . $fieldName
            : '_' . $signature->type->relatedEntity::table() . '_' . $signature->type->invName;
    }
}