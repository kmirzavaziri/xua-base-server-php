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
use XUA\Exceptions\MagicCallException;
use XUA\Exceptions\EntityDeleteException;
use XUA\Exceptions\EntityFieldException;
use XUA\Exceptions\SuperValidationException;
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
 * @method static EntityFieldSignature F_id() The Signature of: Field `id`
 * @method static ConditionField C_id() The Condition Field of: Field `id`
 */
abstract class Entity extends XUA
{
    private static ?PDO $connection = null;

    // TODO remove usages
    final public static function connection() : ?PDO
    {
        return self::$connection;
    }

    final public static function execute(string $query, array $bind = []) : false|PDOStatement
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

    /**
     * @var EntityFieldSignature[][]
     */
    private static array $_x_field_signatures = [];
    private array $_x_fields = [];

    private array $_x_must_fetch = [];
    private array $_x_fetched_by_p = [];
    private array $_x_must_store = [];

    private ?int $_x_given_id;

    /**
     * @throws SuperValidationException
     */
    final public static function _init() : void
    {
        $tableNameTemp = explode("\\", static::class);
        array_shift($tableNameTemp);
        self::$_x_table[static::class] = implode('_', $tableNameTemp);

        self::$_x_field_signatures[static::class] = static::fieldSignaturesCalculator();

        if (!getenv('ENV_NAME') or getenv('ENV_NAME') != 'prod') {
            $dbInfo = ConstantService::get('config/XUA/db.json');
        } else {
            $dbInfo = [
                'engine' => getenv('DB_ENGINE'),
                'hostname' => getenv('DB_HOSTNAME'),
                'database' => getenv('DB_DATABASE'),
                'port' => getenv('DB_PORT'),
                'username' => getenv('DB_USERNAME'),
                'password' => getenv('DB_PASSWORD'),
            ];
        }

        if (!$dbInfo) {
            throw new EntityException('Database connection config not found.');
        }

//        Dialect::$engine = $dbInfo['engine'];

        $dbInfo['dsn'] = $dbInfo['engine'] . ":host=" . $dbInfo['hostname'] . ";port=" . $dbInfo['port']  . ";dbname=" . $dbInfo['database'];

        if (!self::$connection) {
            self::$connection = new PDO($dbInfo['dsn'], $dbInfo['username'], $dbInfo['password']);
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

    final public function __construct(?int $id = null)
    {
        $this->initialize();

        $this->_x_given_id = $id;

        $exists = false;
        if ($id) {
            $statement = self::execute("SELECT EXISTS (SELECT * FROM " . static::table() . " WHERE id = ?) e", [$id]);
            $exists = $statement->fetch()['e'];
        }
        if ($exists) {
            $this->_x_fields['id'] = $id;
        } else {
            foreach (static::fieldSignatures() as $key => $signature) {
                $this->_x_must_store[$key] = true;
            }
        }
        $this->_x_must_fetch['id'] = false;
        $this->_x_must_store['id'] = false;
    }

    /**
     * @throws MagicCallException
     */
    final function __get(string $key)
    {
        $signature = static::fieldSignatures()[$key];

        if ($signature === null) {
            throw (new MagicCallException())->setError($key, 'Unknown entity field');
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

        return $this->_x_fields[$key];
    }

    /**
     * @throws MagicCallException
     */
    function __set(string $key, mixed $value) : void
    {
        $signature = static::fieldSignatures()[$key];

        if ($signature === null) {
            throw (new MagicCallException())->setError($key, 'Unknown entity field');
        }

        if ($key == 'id') {
            throw (new MagicCallException())->setError($key, 'Cannot change id of an entity.');
        }

        if (!$signature->type->accepts($value, $messages)) {
            throw (new MagicCallException())->setError($key, $messages);
        }

        if (is_a($signature->type, PhpVirtualField::class)) {
            if ($signature->type->setter !== null) {
                ($signature->type->setter)($this, $signature->p(), $value);
            } else {
                throw (new MagicCallException())->setError($key, 'Cannot set PhpVirtualField with no setter.');
            }
        }

        if (is_a($signature->type, DatabaseVirtualField::class)) {
            throw (new MagicCallException())->setError($key, 'Cannot set DatabaseVirtualField.');
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

        if ($this->_x_fields[$key] != $value or $this->_x_must_fetch[$key]) {
            $this->_x_fields[$key] = $value;
            $this->_x_must_fetch[$key] = false;
            $this->_x_must_store[$key] = true;
        }
    }

    /**
     * @throws MagicCallException
     */
    public static function __callStatic(string $name, array $arguments)
    {
        if (str_starts_with($name, 'F_')) {
            $conditionField = false;
        } elseif (str_starts_with($name, 'C_')) {
            $conditionField = true;
        } else {
            throw (new MagicCallException("Method $name does not exist."));
        }

        $key = substr($name, 2, strlen($name) - 2);

        if (!isset(static::fieldSignatures()[$key])) {
            throw (new MagicCallException())->setError($key, 'Unknown entity field signature');
        }

        if ($arguments) {
            throw (new MagicCallException())->setError($key, 'An entity field signature method does not accept arguments');
        }

        return $conditionField ? new ConditionField(static::fieldSignatures()[$key]) : static::fieldSignatures()[$key];
    }

    public function __debugInfo(): array
    {
        $result = [];
        foreach ($this->_x_fields as $key => $value) {
            if (!$this->_x_must_fetch[$key]) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    final public static function fieldSignatures() : array {
        return self::$_x_field_signatures[static::class];
    }

    final public function fields() : array {
        return $this->_x_fields;
    }

    final public static function table() : string
    {
        return self::$_x_table[static::class];
    }

    final public function givenId() : ?int
    {
        return $this->_x_given_id;
    }

    # Overridable Methods

    /**
     * @throws SuperValidationException
     */
    protected static function _fieldSignatures() : array
    {
        return [
            'id' => new EntityFieldSignature(static::class, 'id', new Decimal(['unsigned' => true, 'integerLength' => 32, 'base' => 2]), null),
        ];
    }

    protected static function _indexes() : array
    {
        return [
            new Index(['id' => Index::ASC], true, 'PRIMARY'),
        ];
    }

    protected function _validation(EntityFieldException $exception) : void
    {
        # Empty by default
    }

    protected function _initialize(string $caller) : void
    {
        $this->_x_initialize();
    }

    protected static function _getOne(Condition $condition, Order $order, string $caller) : static
    {
        return static::_x_getOne($condition, $order);
    }

    protected function _store(string $caller) : Entity
    {
        return $this->_x_store();
    }

    protected function _delete(string $caller) : void
    {
        $this->_x_delete();
    }

    /**
     * @return static[]
     */
    protected static function _getMany(Condition $condition, Order $order, Pager $pager, string $caller) : array
    {
        return static::_x_getMany($condition, $order, $pager);
    }

    # Overridable Method Wrappers

    /**
     * @throws SuperValidationException
     */
    private static function fieldSignaturesCalculator() : array
    {
        return static::_fieldSignatures();
    }

    final public static function indexes() : array
    {
        $relIndexes = [];
        foreach (static::fieldSignatures() as $key => $signature) {
            if (is_a($signature->type, EntityRelation::class) and $signature->type->relation == 'II' and $signature->type->definedOn == 'here') {
                $relIndexes[] = new Index([$key => 'ASC'], true);
            }
        }
        return array_merge(static::_indexes(), $relIndexes);
    }

    /**
     * @throws EntityFieldException
     */
    private function validation() : void
    {
        $exception = new EntityFieldException();
        $this->_validation($exception);

        foreach (static::fieldSignatures() as $key => $signature) {
            if ($this->_x_must_store[$key] and !$signature->type->accepts($this->_x_fields[$key], $messages)) {
                $exception->setError($key, $messages);
            }
        }

        if ($exception->getErrors()) {
            throw $exception;
        }
    }

    private function initialize(string $caller = Visibility::CALLER_PHP) : void
    {
        $this->_initialize($caller);
    }

    final public static function getOne(?Condition $condition = null, ?Order $order = null, string $caller = Visibility::CALLER_PHP) : static
    {
        if ($condition === null) {
            $condition = Condition::trueLeaf();
        }
        if ($order === null) {
            $order = Order::noOrder();
        }
        return static::_getOne($condition, $order, $caller);
    }

    final public function store(string $caller = Visibility::CALLER_PHP) : Entity
    {
        return $this->_store($caller);
    }

    final public function delete(string $caller = Visibility::CALLER_PHP) : void
    {
        if ($this->id) {
            $this->_delete($caller);
        }
    }

    /**
     * @return static[]
     */
    final public static function getMany(?Condition $condition = null, ?Order $order = null, ?Pager $pager = null, string $caller = Visibility::CALLER_PHP) : array
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

    # Predefined Methods (to wrap in overridable methods)
    final protected function _x_initialize() : void {
        foreach (static::fieldSignatures() as $key => $signature) {
            $this->_x_fields[$key] = $signature->default;
            $this->_x_must_fetch[$key] = true;
            $this->_x_must_store[$key] = false;
            if (is_a($signature->type, PhpVirtualField::class) or is_a($signature->type, DatabaseVirtualField::class)) {
                $this->_x_fetched_by_p[$key] = [];
            }
        }
    }

    final protected static function _x_getOne(Condition $condition, Order $order) : static
    {
        return static::_x_getMany($condition, $order, new Pager(1, 0))[0] ?? new static();
    }

    final protected function _x_store() : Entity
    {
        $this->_x_insert_or_update();
        return $this;
    }

    /**
     * @throws EntityDeleteException
     */
    final protected function _x_delete() : void
    {
        foreach (static::fieldSignatures() as $key => $signature) {
            /** @var EntityFieldSignature $signature */
            if (is_a($signature->type, EntityRelation::class) and $signature->type->relation[0] == 'I' and !$signature->type->invNullable and $this->$key) {
                throw new EntityDeleteException("Cannot delete " . static::table() . " because there exists a $key but the inverse nullable is false.");
            }
        }

        foreach (static::fieldSignatures() as $key => $signature) {
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

    /**
     * @return static[]
     */
    final protected static function _x_getMany(Condition $condition, Order $order, Pager $pager) : array
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

    # Predefined Methods (Array-Entity Conversations)
    final protected function fromDbArray (array $array) : Entity {
        foreach ($array as $key => $value) {
            $signature = static::fieldSignatures()[$key];
            if (is_a($signature->type, EntityRelation::class)) {
                if ($signature->type->relation[1] == 'I') {
                    $result = new $signature->type->relatedEntity($value);
                    if ($result->id) {
                        if ($signature->type->relation[0] == 'I') {
                            $result->_x_fields[$signature->type->invName] = $this;
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
                                    $tmp->_x_fields[$signature->type->invName] = $this;
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
            $this->_x_fields[$key] = $result;
            $this->_x_must_fetch[$key] = false;
            $this->_x_must_store[$key] = false;
        }

        return $this;
    }

    final protected function toDbArray () : array {
        $array = [];
        foreach (static::fieldSignatures() as $key => $signature) {
            /** @var EntityFieldSignature $signature */
            if ($this->_x_must_store[$key] /* @TODO is necessary and $key != 'id' */ and $signature->type->databaseType() != 'DONT STORE') {
                $array[$key] = $signature->type->marshalDatabase($this->_x_fields[$key]);
            }
        }
        return $array;
    }

    # Predefined Methods (low-level direct db communicator)
    private function _x_fetch(?string $fieldName = 'id') : void
    {
        if (!($this->_x_fields['id'] ?? false)) {
            return;
        }

        $signature = static::fieldSignatures()[$fieldName];
        $array = [];

        if (
            is_a($signature->type, EntityRelation::class) and
            $signature->type->relation[1] == 'N'
        ) {
            if ($signature->type->relation == 'IN') {
                $statement = self::execute("SELECT id FROM " . $signature->type->relatedEntity::table() . " WHERE " . $signature->type->invName . " = ?", [$this->_x_fields['id']]);
                $rawArray = $statement->fetchAll(PDO::FETCH_NUM);
                if ($rawArray) {
                    $array[$fieldName] = [];
                    foreach ($rawArray as $item) {
                        $array[$fieldName][] = $item[0];
                    }
                }
            } elseif ($signature->type->relation == 'NN') {
                $statement = self::execute("SELECT " . $signature->type->relatedEntity::table() . " FROM " . static::junctionTableName($fieldName) . " WHERE " . static::table() . " = ?", [$this->_x_fields['id']]);
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
                $statement = self::execute("SELECT $columnsExpression FROM " . static::table() . " WHERE " . static::table() . ".id = ? LIMIT 1", [$this->_x_fields['id']]);
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

    /**
     * @throws EntityFieldException
     */
    private function _x_insert_or_update() : void
    {
        $this->validation();

        $array = $this->toDbArray();

        $query = '';
        $bind = [];
        if ($this->_x_fields['id'] === null) {
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

            $this->_x_fields['id'] = self::connection()->lastInsertId();
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
            $values[] = $this->_x_fields['id'];

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
                    throw (new EntityFieldException())->setError($fieldNames[0], "A $table with $duplicateExpression already exists.");
                } else {
                    throw $e;
                }
            }
        }

        // Take care of relation
        foreach (static::fieldSignatures() as $key => $signature) {
            $value = $this->_x_fields[$key];
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
                    $relINBind[] = $this->_x_fields['id'];
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
                        $relNNBind[] = $this->_x_fields['id'];
                        $relNNBind[] = $addingId;
                    }
                }
                if ($removingIds) {
                    $relNNQuery .= "DELETE FROM " . static::junctionTableName($key) . " WHERE $leftColumn = ? AND $rightColumn IN (?);";
                    $relNNBind[] = $this->_x_fields['id'];
                    $relNNBind[] = $removingIds;
                }

                if ($relNNQuery) {
                    self::execute($relNNQuery, $relNNBind);
                }
            }
        }
    }

    # Predefined Methods (helpers)
    final public static function alter() : array
    {
        $signatures = static::fieldSignatures();
        unset($signatures['id']);

        $tables = [];

        $columns = ['id' => Column::fromQuery("id " . static::fieldSignatures()['id']->type->databaseType() . " NOT NULL AUTO_INCREMENT")];
        foreach ($signatures as $key => $signature) {
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
                $tables[] = new TableScheme(static::junctionTableName($key), [
                    $leftColumn => Column::fromQuery(static::table() . ' ' . $signature->type->relatedEntity::fieldSignatures()['id']->type->databaseType() . " NOT NULL"),
                    $rightColumn => Column::fromQuery($signature->type->relatedEntity::table() . ' ' . $signature->type->relatedEntity::fieldSignatures()['id']->type->databaseType() . " NOT NULL"),
                ], [
                    new Index([$leftColumn => 'ASC', $rightColumn => 'ASC'], true, 'PRIMARY')
                ]);
            }

        }

        $tables[] = new TableScheme(static::table(), $columns, static::indexes());
        $alters = [];
        $tableNames = [];
        foreach ($tables as $table) {
            $tableNames[] = $table->tableName;
            $tmp = $table->alter();
            if ($tmp) {
                $alters[] = $tmp;
            }
        }

        return [
            'tableNames' => $tableNames,
            'alters' => implode(PHP_EOL . PHP_EOL, $alters)
        ];
    }

    private static function columnsExpression(?Entity $entity = null) : array
    {
        $columnExpressions = [];
        $keys = [];
        foreach (static::fieldSignatures() as $key => $signature) {
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

    final public static function junctionTableName (string $fieldName) : string
    {
        $signature = static::fieldSignatures()[$fieldName];
        return $signature->type->definedOn == 'here'
            ? '_' . static::table() . '_' . $fieldName
            : '_' . $signature->type->relatedEntity::table() . '_' . $signature->type->invName;
    }

    private function addThisToAnotherEntity (Entity $entity, string $key) {
        // one-to-? relation
        if ($entity->_x_fields[$key] === null or $entity->_x_fields[$key] instanceof Entity) {
            if ($entity->_x_fields[$key] === null or $entity->_x_fields[$key] !== $this) {
                $entity->_x_fields[$key] = $this;
                $entity->_x_must_fetch[$key] = false;
                $entity->_x_must_store[$key] = true;
            }
        }
        // many-to-? relation
        else {
            $found = false;
            foreach ($entity->_x_fields[$key] as $index => $item) {
                if ($item->_x_fields['id'] == $this->_x_fields['id']) {
                    $entity->_x_fields[$key][$index] = $this;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $entity->_x_fields[$key][] = $this;
            }
            $entity->_x_must_fetch[$key] = false;
            $entity->_x_must_store[$key] = true;
        }
    }

    private function getAddingRemovingIds(string $key) : array
    {
        $currentIds = array_map(function (Entity $entity) {
            return $entity->_x_fields['id'];
        }, $this->_x_fields[$key]);

        $dbIds = array_map(function (Entity $entity) {
            return $entity->_x_fields['id'];
        }, (new static($this->_x_fields['id']))->$key);

        $addingIds = array_diff($currentIds, $dbIds);
        $removingIds = array_diff($dbIds, $currentIds);
        return [$addingIds, $removingIds];
    }
}