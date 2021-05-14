<?php

namespace XUA;

use PDO;
use PDOException;
use PDOStatement;
use ReflectionClass;
use Services\XUA\ConstantService;
use Supers\Basics\EntitySupers\DatabaseVirtualField;
use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\EntitySupers\PhpVirtualField;
use Supers\Basics\Numerics\Decimal;
use XUA\Exceptions\EntityException;
use XUA\Exceptions\EntityFieldException;
use XUA\Tools\Entity\Column;
use XUA\Tools\Entity\Condition;
use XUA\Tools\Entity\ConditionField;
use XUA\Tools\Signature\EntityFieldSignature;
use XUA\Tools\Entity\Index;
use XUA\Tools\Entity\Order;
use XUA\Tools\Entity\Pager;
use XUA\Tools\Entity\TableScheme;
use XUA\Tools\Visibility;

/**
 * @property int id
 */
abstract class Entity extends XUA
{
    const id = 'id';

    private static ?PDO $connection = null;

    final public static function connection() : ?PDO
    {
        return self::$connection;
    }

    final public static function execute(string $query, array $bind) : false|PDOStatement
    {
        $arrayBindPositions = [];
        $pos = 0;
        $newBind = [];
        foreach ($bind as $value) {
            $pos = strpos($query, '?', $pos);
            if (is_array($value)) {
                $arrayBindPositions[$pos] = count($value);
                $newBind   = array_merge($newBind, $value);
            } else {
                $newBind[] = $value;
            }
            $pos++;
        }
        if ($arrayBindPositions) {
            $newQuery = '';
            $start = 0;
            foreach ($arrayBindPositions as $pos => $count) {
                $newQuery .= substr($query, $start, $pos - $start);
                $newQuery .= implode(',', array_fill(0, $count, '?'));
                $start = $pos + 1;
            }
            $newQuery .= substr($query, $start);
            $query = $newQuery;
        }
        $bind = $newBind;

        try {
            $statement = self::connection()->prepare($query);
            $statement->execute($bind);
        } catch (PDOException $e) {
            $boundQuery = '';
            $start = 0;
            $i = 0;
            while (($pos = strpos($query, '?', $start)) !== false) {
                $boundQuery .= substr($query, $start, $pos - $start) . ("'$bind[$i]'" ?? '?');
                $i++;
                $start = $pos + 1;
            }
            $boundQuery .= substr($query, $start);

            $messageProperty = (new ReflectionClass(PDOException::class))->getProperty('message');
            $messageProperty->setAccessible(true);
            $messageProperty->setValue($e, $e->getMessage() . PHP_EOL . $boundQuery);
            throw $e;
        }

        return $statement;
    }

    # Magics
    private static array $_x_table = [];

    private static array $_x_structure = [];
    private array $_x_properties = [];

    private array $_x_must_fetch = [];
    private array $_x_fetched_by_p = [];
    private array $_x_must_store = [];

    private ?int $_x_given_id = null;

    /* DONE */ final public static function _init() : void
    {
        $tableNameTemp = explode("\\", static::class);
        array_shift($tableNameTemp);
        self::$_x_table[static::class] = implode('', $tableNameTemp);

        self::$_x_structure[static::class] = static::fields();

        if (!self::$connection) {
            self::$connection = new PDO(ConstantService::CONNECTION_DSN, ConstantService::CONNECTION_USERNAME, ConstantService::CONNECTION_PASSWORD);
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

    /* DONE */ final public function __construct(?int $id = null)
    {
        $this->initialize();

        $this->_x_given_id = $id;

        $exists = false;
        if ($id) {
            $statement = self::execute("SELECT EXISTS (SELECT * FROM " . static::table() . " WHERE id = ?) e", [$id]);
            $exists = $statement->fetch()['e'];
        }
        if ($exists) {
            $this->_x_properties['id'] = $id;
            $this->_x_must_fetch['id'] = false;
            $this->_x_must_store['id'] = false;
        } else {
            foreach (static::structure() as $key => $signature) {
                $this->_x_must_store[$key] = true;
            }
            $this->_x_must_fetch['id'] = false;
            $this->_x_must_store['id'] = false;
        }
    }

    /* DONE */ function __get(string $key)
    {
        $signature = static::F($key);

        if ($signature === null) {
            throw new EntityFieldException("$key is not in (" . implode(', ', array_keys(static::structure())) . ")");
        }

        if (
            (is_a($signature->type, PhpVirtualField::class) or is_a($signature->type, DatabaseVirtualField::class)) and
            $this->_x_fetched_by_p[$key] != $signature->p()
        ) {
            $this->_x_must_fetch[$key] = true;
        }

        if ($this->_x_must_fetch[$key]) {
            $this->_x_fetch($key);
        }

        return $this->_x_properties[$key];
    }

    /* DONE */ function __set(string $key, mixed $value) : void
    {
        $signature = static::F($key);

        if ($signature === null) {
            throw new EntityFieldException("'$key' is not in " . implode(', ', array_keys(static::structure())) . ".");
        }

        if ($key == 'id') {
            throw new EntityFieldException("Cannot set field 'id' of an entity.");
        }

        if (!$signature->type->accepts($value, $messages)) {
            throw new EntityFieldException("$key: " . implode(" ", $messages));
        }

        if (is_a($signature->type, PhpVirtualField::class)) {
            if ($signature->type->setter !== null) {
                ($signature->type->setter)($this, $signature->p(), $value);
            } else {
                throw new EntityFieldException('Cannot set PhpVirtualField with no setter.');
            }
        }

        if (is_a($signature->type, DatabaseVirtualField::class)) {
            throw new EntityFieldException('Cannot set DatabaseVirtualField.');
        }

        if (is_a($signature->type, EntityRelation::class)) {
            if ($signature->type->relation[1] == 'I') {
                if ($value !== null) {
                    $this->addThisToAnotherEntity($value, $signature->type->invName);
                }
            } elseif ($signature->type->relation[1] == 'N') {
                foreach ($value as $item) {
                    $this->addThisToAnotherEntity($item, $signature->type->invName);
                }
            }
        }

        if ($this->_x_properties[$key] != $value or $this->_x_must_fetch[$key]) {
            $this->_x_properties[$key] = $value;
            $this->_x_must_fetch[$key] = false;
            $this->_x_must_store[$key] = true;
        }
    }

    /* DONE */ public function __debugInfo(): array
    {
        $result = [];
        foreach ($this->_x_properties as $key => $value) {
            if (!$this->_x_must_fetch[$key]) {
                $result[$key] = $value;
            }
        }
        return $result;
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

    /* DONE */ final public function givenId() : ?int
    {
        return $this->_x_given_id;
    }

    # Overridable Methods
    /* DONE */ protected static function _fields() : array
    {
        return [
            'id' => new EntityFieldSignature(static::class, 'id', new Decimal(['unsigned' => true, 'integerLength' => 32, 'base' => 2]), null),
        ];
    }

    /* DONE */ protected static function _indexes() : array
    {
        return [
            new Index(['id' => Index::ASC], true, 'PRIMARY'),
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

    /* DONE */ protected function _delete(string $caller) : void
    {
        $this->_x_delete();
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
        $relIndexes = [];
        foreach (static::structure() as $key => $signature) {
            if (is_a($signature->type, EntityRelation::class) and $signature->type->relation == 'II' and $signature->type->definedOn == 'here') {
                $relIndexes[] = new Index([$key => 'ASC'], true);
            }
        }
        return array_merge(static::_indexes(), $relIndexes);
    }

    /* DONE */ private function validation() : void
    {
        $this->_validation();

        foreach (static::structure() as $key => $signature) {
            /** @var EntityFieldSignature $signature */
            if ($this->_x_must_store[$key] and !$signature->type->accepts($this->_x_properties[$key], $messages)) {
                throw new EntityFieldException("$key: " . implode(" ", $messages));
            }
        }

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

    /* DONE */ final public function delete(string $caller = Visibility::CALLER_PHP) : void
    {
        if ($this->id) {
            $this->_delete($caller);
        }
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
            $this->_x_must_fetch[$key] = true;
            $this->_x_must_store[$key] = false;
            if (is_a($signature->type, PhpVirtualField::class) or is_a($signature->type, DatabaseVirtualField::class)) {
                $this->_x_fetched_by_p[$key] = [];
            }
        }
    }

    /* DONE */ final protected static function _x_getOne(Condition $condition, Order $order) : Entity
    {
        return static::_x_getMany($condition, $order, new Pager(1, 0))[0] ?? new static();
    }

    /* DONE */ final protected function _x_store() : Entity
    {
        $this->_x_insert_or_update();
        return $this;
    }

    /* DONE */ final protected function _x_delete() : void
    {
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
                    if ($this->$key) {
                        $this->$key->{$signature->type->invName} = null;
                        $this->$key->store();
                    }
                } elseif ($signature->type->relation == 'NN') {
                    $this->$key = [];
                    $this->store();
                }
            }
        }

        self::execute("DELETE FROM " . static::table() . " WHERE id = ? LIMIT 1", [$this->id]);
    }

    /* DONE */ final protected static function _x_getMany(Condition $condition, Order $order, Pager $pager) : array
    {
        [$columnsExpression, $keys] = self::columnsExpression();
        $statement = self::execute("SELECT $columnsExpression FROM " . static::table() . " " . $condition->joins() . " WHERE $condition->template " . $order->render() . $pager->render(), $condition->parameters);
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
            $entities[] = (new static())->fromDbArray($array);
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
    /* DONE */ final protected function fromDbArray (array $array) : Entity {
        foreach ($array as $key => $value) {
            $signature = static::F($key);
            if (is_a($signature->type, EntityRelation::class)) {
                if ($signature->type->relation[1] == 'I') {
                    $result = new $signature->type->relatedEntity($value);
                    if ($result->id) {
                        if ($signature->type->relation[0] == 'I') {
                            $result->_x_properties[$signature->type->invName] = $this;
                            $result->_x_must_fetch[$signature->type->invName] = false;
                            $result->_x_must_store[$signature->type->invName] = false;
                        }
                    }
                } elseif ($signature->type->relation[1] == 'N') {
                    $result = [];
                    if ($value) {
                        foreach ($value as $id) {
                            $tmp = new $signature->type->relatedEntity($id);
                            if ($tmp->id) {
                                if ($signature->type->relation[0] == 'I') {
                                    $tmp->_x_properties[$signature->type->invName] = $this;
                                    $tmp->_x_must_fetch[$signature->type->invName] = false;
                                    $tmp->_x_must_store[$signature->type->invName] = false;
                                }
                                $result[] = $tmp;
                            }
                        }
                    }
                }
            } elseif (is_a($signature->type, PhpVirtualField::class) or is_a($signature->type, DatabaseVirtualField::class)) {
                $this->_x_fetched_by_p[$key] = $signature->p();
                $result = $value;
            } else {
                $result = $signature->type->unmarshalDatabase($value);
            }
            $this->_x_properties[$key] = $result;
            $this->_x_must_fetch[$key] = false;
            $this->_x_must_store[$key] = false;
        }

        return $this;
    }

    /* DONE */ final protected function toDbArray () : array {
        $array = [];
        foreach (static::structure() as $key => $signature) {
            /** @var EntityFieldSignature $signature */
            if ($this->_x_must_store[$key] /* @TODO is necessary and $key != 'id' */ and $signature->type->databaseType() != 'DONT STORE') {
                $array[$key] = $signature->type->marshalDatabase($this->_x_properties[$key]);
            }
        }
        return $array;
    }

    # Predefined Methods (low-level direct mysql communicator)
    /* DONE */ private function _x_fetch(?string $fieldName = 'id') : void
    {
        if (!($this->_x_properties['id'] ?? false)) {
            return;
        }

        $signature = static::F($fieldName);
        $array = [];

        if (
            is_a($signature->type, EntityRelation::class) and
            $signature->type->relation[1] == 'N'
        ) {
            if ($signature->type->relation == 'IN') {
                $statement = self::execute("SELECT id FROM " . $signature->type->relatedEntity::table() . " WHERE " . $signature->type->invName . " = ?", [$this->_x_properties['id']]);
                $rawArray = $statement->fetchAll(PDO::FETCH_NUM);
                if ($rawArray) {
                    $array[$fieldName] = [];
                    foreach ($rawArray as $item) {
                        $array[$fieldName][] = $item[0];
                    }
                }
            } elseif ($signature->type->relation == 'NN') {
                $statement = self::execute("SELECT " . $signature->type->relatedEntity::table() . " FROM " . static::junctionTableName($fieldName) . " WHERE " . static::table() . " = ?", [$this->_x_properties['id']]);
                $rawArray = $statement->fetchAll(PDO::FETCH_NUM);
                if ($rawArray) {
                    $array[$fieldName] = [];
                    foreach ($rawArray as $item) {
                        $array[$fieldName][] = $item[0];
                    }
                }
            }
        } elseif (is_a($signature->type, PhpVirtualField::class)) {
            $array[$fieldName] = ($signature->type->getter)($this, $signature->p());
        } else {
            [$columnsExpression, $keys] = self::columnsExpression($this);
            if ($columnsExpression) {
                $statement = self::execute("SELECT $columnsExpression FROM " . static::table() . " WHERE " . static::table() . ".id = ? LIMIT 1", [$this->_x_properties['id']]);
                $rawArray = $statement->fetch(PDO::FETCH_NUM);
                if ($rawArray) {
                    foreach ($keys as $i => $key) {
                        $array[$key] = $rawArray[$i];
                    }
                }
            }
        }

        $this->fromDbArray($array);
    }

    private function _x_insert_or_update() : void
    {
        $this->validation();

        $array = $this->toDbArray();

        $query = '';
        $bind = [];
        if ($this->_x_properties['id'] === null) {
            $columnNames = [];
            $placeHolders = [];
            $values = [];
            foreach ($array as $key => $value) {
                $columnNames[] = $key;
                $placeHolders[] = '?';
                $values[] = $value;
            }

            $columnNames = implode(', ', $columnNames);
            $placeHolders = implode(', ', $placeHolders);

            $query = "INSERT INTO " . static::table() . " ($columnNames) VALUES ($placeHolders)";
            $bind = $values;

            $this->_x_properties['id'] = self::connection()->lastInsertId();
            $this->_x_must_fetch['id'] = false;
            $this->_x_must_store['id'] = false;
        } else {
            $expressions = [];
            $values = [];
            foreach ($array as $key => $value) {
                $expressions[] = "$key = ?";
                $values[] = $value;
            }

            $expressions = implode(', ', $expressions);
            $values[] = $this->_x_properties['id'];

            if ($expressions) {
                $query = "UPDATE " . static::table() . " SET $expressions WHERE id = ?";
                $bind = $values;
            }
        }

        if ($query and $bind) {
            try {
                self::execute($query, $bind);
            } catch (PDOException $e) {
                // TODO check $e->getCode() instead of str_contains
                if (str_contains($e->getMessage(), 'Duplicate entry')) {
                    $pattern = "/Duplicate entry '([^']*)' for key '([^.]*)\.([^']*)'/";
                    preg_match($pattern, $e->getMessage(), $matches);
                    $duplicateValues = explode('-', $matches[1]);
                    $table = $matches[2];
                    $duplicateIndexName = $matches[3];
                    $duplicateIndexes = array_filter(static::indexes(), function (Index $index) use($duplicateIndexName) {
                        return $index->name == $duplicateIndexName;
                    });
                    $duplicateIndex = array_pop($duplicateIndexes);
                    $duplicateExpressions = [];
                    $iterator = 0;
                    $fieldNames = array_keys($duplicateIndex->fields);
                    foreach ($fieldNames as $fieldName) {
                        $duplicateExpressions[] = "$fieldName=" . $duplicateValues[$iterator];
                        $iterator++;
                    }
                    $duplicateExpression = implode(', ', $duplicateExpressions);
                    throw new EntityFieldException("$fieldNames[0]: A $table with $duplicateExpression already exists.");
                } else {
                    throw new PDOException($e->getMessage(), $e->getCode());
                }
            }
        }

        // Take care of relation
        foreach (static::structure() as $key => $signature) {
            $value = $this->_x_properties[$key];
            if (!is_a($signature->type, EntityRelation::class) or !$this->_x_must_store[$key]) {
                continue;
            } elseif ($signature->type->relation == 'II' and $signature->type->definedOn == 'there') {
                $value->store();
            } elseif ($signature->type->relation == 'IN') {
                [$addingIds, $removingIds] = $this->getAddingRemovingIds($key);

                // @TODO verify inverse is nullable if removing ids
                $relINQuery = '';
                $relINBind = [];
                if ($addingIds) {
                    $relINQuery .= "UPDATE " . $signature->type->relatedEntity::table() . " SET " . $signature->type->invName . " = ? WHERE id IN (?);";
                    $relINBind[] = $this->_x_properties['id'];
                    $relINBind[] = $addingIds;
                }
                if ($removingIds) {
                    $relINQuery .= "UPDATE " . $signature->type->relatedEntity::table() . " SET " . $signature->type->invName . " = NULL WHERE id IN (?);";
                    $relINBind[] = $removingIds;
                }

                if ($relINQuery) {
                    self::execute($relINQuery, $relINBind);
                }
            } elseif ($signature->type->relation == 'NN') {
                [$addingIds, $removingIds] = $this->getAddingRemovingIds($key);
                $leftColumn = static::table();
                $rightColumn = $signature->type->relatedEntity::table();

                $relNNQuery = '';
                $relNNBind = [];
                if ($addingIds) {
                    $relNNQuery .= "INSERT INTO " . static::junctionTableName($key) . " ($leftColumn, $rightColumn) VALUES\n" .
                        implode(",\n", array_fill(0, count($addingIds), "\t(?, ?)")) . ";\n";
                    foreach ($addingIds as $addingId) {
                        $relNNBind[] = $this->_x_properties['id'];
                        $relNNBind[] = $addingId;
                    }
                }
                if ($removingIds) {
                    $relNNQuery .= "DELETE FROM " . static::junctionTableName($key) . " WHERE $leftColumn = ? AND $rightColumn IN (?);";
                    $relNNBind[] = $this->_x_properties['id'];
                    $relNNBind[] = $removingIds;
                }

                if ($relNNQuery) {
                    self::execute($relNNQuery, $relNNBind);
                }
            }
        }
    }

    # Predefined Methods (helpers)
    /* DONE */ final public static function alter() : string
    {
        $signatures = static::structure();
        unset($signatures['id']);

        $tables = [];

        $columns = ['id' => Column::fromQuery("id " . static::F('id')->type->databaseType() . " NOT NULL AUTO_INCREMENT")];
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
                $leftColumn = static::table();
                $rightColumn = $signature->type->relatedEntity::table();
                $tables[] = new TableScheme('_' . static::table() . '_' . $key, [
                    $leftColumn => Column::fromQuery(static::table() . ' ' . $signature->type->relatedEntity::F('id')->type->databaseType() . " NOT NULL"),
                    $rightColumn => Column::fromQuery($signature->type->relatedEntity::table() . ' ' . $signature->type->relatedEntity::F('id')->type->databaseType() . " NOT NULL"),
                ], [
                    new Index([$leftColumn => 'ASC', $rightColumn => 'ASC'], true, 'PRIMARY')
                ]);
            }

        }

        $tables[] = new TableScheme(static::table(), $columns, static::indexes());
        $alters = [];
        foreach ($tables as $table) {
            $tmp = $table->alter();
            if ($tmp) {
                $alters[] = $tmp;
            }
        }

        return implode(PHP_EOL . PHP_EOL, $alters) . PHP_EOL . PHP_EOL;
    }

    /* DONE */ private static function columnsExpression(?Entity $entity = null) : array
    {
        $columnExpressions = [];
        $keys = [];
        foreach (static::structure() as $key => $signature) {
            /** @var EntityFieldSignature $signature */
            if ($entity and !$entity->_x_must_fetch[$key]) {
                continue;
            }
            if (is_a($signature->type, DatabaseVirtualField::class)) {
                $expression =  ($signature->type->getter)($signature->p()) . ' ' . $signature->name;
            } elseif ($signature->type->databaseType() != 'DONT STORE') {
                $expression = static::table() . '.' . $signature->name;
            } else {
                continue;
            }

            $columnExpressions[] = $expression;
            $keys[] = $key;
        }

        $columnsExpression = implode(', ', $columnExpressions);

        return [$columnsExpression, $keys];
    }

    /* DONE */ final public static function junctionTableName (string $fieldName) : string
    {
        $signature = static::F($fieldName);
        return $signature->type->definedOn == 'here'
            ? '_' . static::table() . '_' . $fieldName
            : '_' . $signature->type->relatedEntity::table() . '_' . $signature->type->invName;
    }

    /* DONE */ private function addThisToAnotherEntity (Entity &$entity, string $key) {
        // one-to-? relation
        if ($entity->_x_properties[$key] === null or $entity->_x_properties[$key] instanceof Entity) {
            if ($entity->_x_properties[$key] === null or $entity->_x_properties[$key] !== $this) {
                $entity->_x_properties[$key] = $this;
                $entity->_x_must_fetch[$key] = false;
                $entity->_x_must_store[$key] = true;
            }
        }
        // many-to-? relation
        else {
            $found = false;
            foreach ($entity->_x_properties[$key] as $index => $item) {
                if ($item->_x_properties['id'] == $this->_x_properties['id']) {
                    $entity->_x_properties[$key][$index] = $this;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $entity->_x_properties[$key][] = $this;
            }
            $entity->_x_must_fetch[$key] = false;
            $entity->_x_must_store[$key] = true;
        }
    }

    /* DONE */ private function getAddingRemovingIds(string $key) : array
    {
        $currentIds = array_map(function (Entity $entity) {
            return $entity->_x_properties['id'];
        }, $this->_x_properties[$key]);

        $dbIds = array_map(function (Entity $entity) {
            return $entity->_x_properties['id'];
        }, (new static($this->_x_properties['id']))->$key);

        $addingIds = array_diff($currentIds, $dbIds);
        $removingIds = array_diff($dbIds, $currentIds);
        return [$addingIds, $removingIds];
    }

    /* DONE */ final public static function F(string $key) : ?EntityFieldSignature
    {
        return static::structure()[$key] ?? null;
    }
    /* DONE */ final public static function CF(string $key) : ?ConditionField
    {
        return static::F($key) ? new ConditionField(static::F($key)) : null;
    }
}