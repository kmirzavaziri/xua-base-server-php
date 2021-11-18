<?php

namespace Xua\Core\Eves;

use JetBrains\PhpStorm\ArrayShape;
use PDO;
use PDOException;
use PDOStatement;
use ReflectionClass;
use ReflectionException;
use Xua\Core\Exceptions\EntityConditionException;
use Xua\Core\Exceptions\NotImplementedException;
use Xua\Core\Exceptions\SuperMarshalException;
use Xua\Core\Services\ConstantService;
use Xua\Core\Services\ExpressionService;
use Xua\Core\Supers\Special\DatabaseVirtualField;
use Xua\Core\Supers\Special\EntityRelation;
use Xua\Core\Supers\Special\OrderScheme;
use Xua\Core\Supers\Special\PhpVirtualField;
use Throwable;
use Xua\Core\Exceptions\EntityException;
use Xua\Core\Exceptions\MagicCallException;
use Xua\Core\Exceptions\EntityDeleteException;
use Xua\Core\Exceptions\EntityFieldException;
use Xua\Core\Exceptions\SuperValidationException;
use Xua\Core\Supers\Numerics\Identifier;
use Xua\Core\Tools\Entity\EntityBuffer;
use Xua\Core\Tools\Entity\EntityLock;
use Xua\Core\Tools\Entity\Query;
use Xua\Core\Tools\Entity\QueryBinder;
use Xua\Core\Tools\Entity\Column;
use Xua\Core\Tools\Entity\Condition;
use Xua\Core\Tools\Entity\CF;
use Xua\Core\Tools\Entity\Order;
use Xua\Core\Tools\Entity\Pager;
use Xua\Core\Tools\Entity\TableScheme;
use Xua\Core\Tools\Signature\Signature;
use Xua\Core\Tools\Visibility;

/**
 * @property mixed id
 */
abstract class Entity extends Block
{
    const id = 'Xua\\Core\\Eves\\Entity::id';

    ####################################################################################################################
    # Database Engine Connection #######################################################################################
    ####################################################################################################################
    /**
     * @var PDO|null
     */
    private static ?PDO $connection = null;

    /**
     * @var PDOException
     */
    private static PDOException $connection_exception;

    // @TODO remove usages
    /**
     * @return PDO|null
     */
    final public static function connection(): ?PDO
    {
        return self::$connection;
    }

    /**
     * @param string $query
     * @param array $bind
     * @return false|PDOStatement
     * @throws EntityException
     * @throws EntityFieldException
     */
    final public static function execute(string $query, array $bind = []): false|PDOStatement
    {
        [$query, $bind] = QueryBinder::getQueryAndBind($query, $bind);
        if (!self::connection()) {
            throw self::$connection_exception;
        }
        try {
            $statement = self::connection()->prepare($query);
            $statement->execute($bind);
            return $statement;
        } catch (PDOException $e) {
            static::handlePDOException($e, QueryBinder::bind($query, $bind));
            return false;
        }
    }

    ####################################################################################################################
    # Magics ###########################################################################################################
    ####################################################################################################################
    /**
     * @var array
     */
    private static array $_x_table = [];

    /**
     * @var array
     */
    private array $_x_must_fetch = [];

    /**
     * @var array
     */
    private array $_x_fetched_by_p = [];

    /**
     * @var array
     */
    private array $_x_must_store = [];

    /**
     * @var int|null
     */
    private ?int $_x_given_id;

    /**
     * @var int
     */
    private static int $_x_lastSavepointNo = 0;

    /**
     * @throws EntityException
     */
    final public static function _init(): void
    {
        parent::_init();
        $tableNameTemp = explode("\\", static::class);
        self::$_x_table[static::class] = implode('_', $tableNameTemp);

        $dbInfo = ConstantService::get('config', 'db');
        if (!$dbInfo) {
            throw new EntityException('Database connection config not found.');
        }

//        Dialect::$engine = $dbInfo['engine'];

        $dbInfo['dsn'] = $dbInfo['engine'] . ":host=" . $dbInfo['hostname'] . ";port=" . $dbInfo['port']  . ";dbname=" . $dbInfo['database'];

        if (!self::$connection) {
            try {
                self::$connection = new PDO($dbInfo['dsn'], $dbInfo['username'], $dbInfo['password']);
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                self::$connection_exception = $e;
                throw $e;
            }
        }
    }

    /**
     * @param int|null $id
     * @throws EntityFieldException
     */
    final public function __construct(?int $id = null)
    {
        $this->initialize();

        $this->_x_given_id = $id;

        $exists = false;
        if ($id) {
            $statement = self::execute("SELECT EXISTS (SELECT * FROM `" . static::table() . "` WHERE `id` = ?) e", [$id]);
            $exists = $statement->fetch()['e'];
        }
        if ($exists) {
            $this->_x_values[self::FIELD_PREFIX]['id'] = $id;
        } else {
            foreach (static::fieldSignatures() as $key => $signature) {
                $this->_x_must_store[$key] = true;
            }
        }
        $this->_x_must_fetch['id'] = false;
        $this->_x_must_store['id'] = false;
    }

    /**
     * @return array
     */
    public function __debugInfo(): array
    {
        $result = [];
        foreach ($this->_x_values[self::FIELD_PREFIX] as $key => $value) {
            if (!$this->_x_must_fetch[$key]) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * @return string
     */
    final public static function table(): string
    {
        return self::$_x_table[static::class];
    }

    /**
     * @return int|null
     */
    final public function givenId(): ?int
    {
        return $this->_x_given_id;
    }

    ####################################################################################################################
    # Signatures #######################################################################################################
    ####################################################################################################################
    const FIELD_PREFIX = '';
    const INDEX_PREFIX = 'I_';

    /**
     *
     */
    protected static function registerSignatures(): void
    {
        parent::registerSignatures();
        Signature::registerSignatures(static::class, self::FIELD_PREFIX, Signature::associate(static::_fieldSignatures()));
        Signature::registerSignatures(static::class, self::INDEX_PREFIX, static::_fieldSignatures());
    }

    /**
     * @param string $prefix
     * @param string $name
     * @param Signature $signature
     * @param mixed $value
     * @throws EntityFieldException
     */
    final protected function getterProcedure(string $prefix, string $name, Signature $signature, mixed $value): void
    {
        if (
            (is_a($signature->declaration, PhpVirtualField::class) or is_a($signature->declaration, DatabaseVirtualField::class)) and
            $this->_x_fetched_by_p[$name] != $signature->p()
        ) {
            $this->_x_must_fetch[$name] = true;
        }

        if ($this->_x_must_fetch[$name]) {
            $this->_x_fetch($name);
        }
    }

    /**
     * @param string $prefix
     * @param string $name
     * @param Signature $signature
     * @param mixed $value
     * @throws EntityFieldException
     * @throws MagicCallException
     */
    final protected function setterProcedure(string $prefix, string $name, Signature $signature, mixed $value): void
    {
        if ($name == 'id') {
            throw (new MagicCallException())->setError($name, 'Cannot change id of an entity.');
        }

        if (is_a($signature->declaration, PhpVirtualField::class)) {
            if ($signature->declaration->setter !== null) {
                ($signature->declaration->setter)($this, $signature->p(), $value);
            } else {
                throw (new MagicCallException())->setError($name, 'Cannot set PhpVirtualField with no setter.');
            }
        }

        if (is_a($signature->declaration, DatabaseVirtualField::class)) {
            throw (new MagicCallException())->setError($name, 'Cannot set DatabaseVirtualField.');
        }

        if (is_a($signature->declaration, EntityRelation::class)) {
            if ($signature->declaration->toOne) {
                if ($value !== null and $signature->declaration->invName !== null) {
                    $this->addThisToAnotherEntity($value, $signature->declaration->invName);
                }
            } elseif ($signature->declaration->toMany) {
                if ($signature->declaration->invName !== null) {
                    foreach ($value as $item) {
                        $this->addThisToAnotherEntity($item, $signature->declaration->invName);
                    }
                }
            }
        }

        if (!$signature->declaration->accepts($value, $messages)) {
            throw (new EntityFieldException())->setError($name, $messages['identity']);
        }

        if ($this->_x_values[self::FIELD_PREFIX][$name] != $value or $this->_x_must_fetch[$name] or is_object($this->_x_values[self::FIELD_PREFIX][$name])) {
            $this->_x_must_fetch[$name] = false;
            $this->_x_must_store[$name] = true;
        }
    }

    /**
     * @return Signature[]
     */
    final public static function fieldSignatures() : array
    {
        return Signature::signatures(static::class, self::FIELD_PREFIX);
    }

    /**
     * @return Signature[]
     * @throws SuperValidationException
     */
    protected static function _fieldSignatures(): array
    {
        return [
            Signature::new(null, static::id, null, null, new Identifier([])),
        ];
    }

    /**
     * @return Signature[]
     */
    final public static function indexSignatures(): array
    {
        $relIndexes = [];
        foreach (static::fieldSignatures() as $key => $signature) {
            if (is_a($signature->declaration, EntityRelation::class) and $signature->declaration->columnHere) {
                $relIndexes[] = Signature::new(null, null, null, null, new OrderScheme([
                    OrderScheme::fields => [
                        [
                            OrderScheme::direction => OrderScheme::DIRECTION_ASC,
                            OrderScheme::field => $key,
                        ]
                    ],
                    OrderScheme::unique => $signature->declaration->fromOne,
                ]));
            }
        }
        return array_merge(static::_indexSignatures(), $relIndexes);
    }

    /**
     * @return Signature[]
     */
    protected static function _indexSignatures(): array
    {
        return [
            Signature::new(null, null, null, null, new OrderScheme([
                OrderScheme::fields => [
                    [
                        OrderScheme::direction => OrderScheme::DIRECTION_ASC,
                        OrderScheme::field => Signature::_(static::id)->name,
                    ],
                ],
                OrderScheme::unique => true,
                OrderScheme::name => 'PRIMARY',
            ])),
        ];
    }


    ####################################################################################################################
    # Overridable Methods ##############################################################################################
    ####################################################################################################################
    /**
     * @param EntityFieldException $exception
     */
    protected function _validation(EntityFieldException $exception): void
    {
        // Empty by default
    }

    /**
     *
     */
    protected function _initialize(): void
    {
        $this->_x_initialize();
    }

    /**
     * @param Condition $condition
     * @param Order $order
     * @param string $lock
     * @param string $caller
     * @return static
     * @throws EntityFieldException
     * @throws ReflectionException
     * @noinspection PhpUnusedParameterInspection
     */
    protected static function _getOne(Condition $condition, Order $order, string $lock, string $caller): static
    {
        return static::_x_getOne($condition, $order, $lock);
    }

    /**
     * @param string $caller
     * @throws EntityException
     * @throws EntityFieldException
     * @throws Throwable
     * @noinspection PhpUnusedParameterInspection
     */
    protected function _store(string $caller): void
    {
        $this->_x_store();
    }

    /**
     * @param string $caller
     * @return array
     * @throws EntityConditionException
     * @throws EntityException
     * @throws EntityFieldException
     * @throws ReflectionException
     * @throws SuperMarshalException
     * @throws SuperValidationException
     * @noinspection PhpUnusedParameterInspection
     */
    protected function _storeQueries(string $caller): array
    {
        return $this->_x_storeQueries();
    }

    /**
     * @param string $caller
     * @throws EntityDeleteException
     * @throws EntityException
     * @throws EntityFieldException
     * @throws Throwable
     * @noinspection PhpUnusedParameterInspection
     */
    protected function _delete(string $caller): void
    {
        $this->_x_delete();
    }

    /**
     * @param Condition $condition
     * @param Order $order
     * @param Pager $pager
     * @param string $lock
     * @param string $caller
     * @return static[]
     * @throws EntityFieldException
     * @throws ReflectionException
     * @noinspection PhpUnusedParameterInspection
     */
    protected static function _getMany(Condition $condition, Order $order, Pager $pager, string $lock, string $caller): array
    {
        return static::_x_getMany($condition, $order, $pager, $lock);
    }

    /**
     * @param Condition $condition
     * @param Order $order
     * @param Pager $pager
     * @param string $caller
     * @return int
     * @throws EntityFieldException
     * @noinspection PhpUnusedParameterInspection
     */
    protected static function _count(Condition $condition, Order $order, Pager $pager, string $caller): int
    {
        return static::_x_count($condition, $order, $pager);
    }

    /**
     * @param Condition $condition
     * @param Order $order
     * @param Pager $pager
     * @param string $caller
     * @return int
     * @throws EntityFieldException
     * @noinspection PhpUnusedParameterInspection
     */
    protected static function _deleteMany(Condition $condition, Order $order, Pager $pager, string $caller): int
    {
        return static::_x_deleteMany($condition, $order, $pager);
    }

    /**
     * @param PDOException $e
     * @param string $query
     * @throws EntityFieldException
     */
    protected static function _handlePDOException(PDOException $e, string $query): void
    {
        static::_x_handlePDOException($e, $query);
    }

    /**
     * @param array $change
     * @param Condition $condition
     * @param Order $order
     * @param Pager $pager
     * @param string $caller
     * @return int
     * @throws NotImplementedException
     * @noinspection PhpUnusedParameterInspection
     */
    protected static function _setMany(array $change, Condition $condition, Order $order, Pager $pager, string $caller): int
    {
        // @TODO Implement
        throw new NotImplementedException();
    }

    ####################################################################################################################
    # Overridable Method Wrappers ######################################################################################
    ####################################################################################################################
    /**
     * @throws EntityFieldException
     */
    private function validation(): void
    {
        $exception = new EntityFieldException();
        $this->_validation($exception);

        foreach (static::fieldSignatures() as $key => $signature) {
            if ($this->_x_must_store[$key] and !$signature->declaration->accepts($this->_x_values[self::FIELD_PREFIX][$key], $messages)) {
                $exception->setError($key, $messages);
            }
        }

        if ($exception->getErrors()) {
            throw $exception;
        }
    }

    /**
     * @return $this
     */
    private function initialize(): static
    {
        $this->_initialize();
        return $this;
    }

    /**
     * @param Condition|null $condition
     * @param Order|null $order
     * @param string $lock
     * @param string $caller
     * @return static
     * @throws EntityFieldException
     * @throws ReflectionException
     */
    final public static function getOne(?Condition $condition = null, ?Order $order = null, string $lock = EntityLock::DEFAULT, string $caller = Visibility::CALLER_PHP): static
    {
        if ($condition === null) {
            $condition = Condition::trueLeaf();
        }
        if ($order === null) {
            $order = Order::noOrder();
        }
        return static::_getOne($condition, $order, $lock, $caller);
    }

    /**
     * @param string $caller
     * @return $this
     * @throws EntityException
     * @throws EntityFieldException
     * @throws Throwable
     */
    final public function store(string $caller = Visibility::CALLER_PHP): static
    {
        $this->_store($caller);
        return $this;
    }

    /**
     * @param string $caller
     * @return array
     * @throws EntityConditionException
     * @throws EntityException
     * @throws EntityFieldException
     * @throws ReflectionException
     * @throws SuperMarshalException
     * @throws SuperValidationException
     */
    final protected function storeQueries(string $caller = Visibility::CALLER_PHP): array
    {
        return $this->_storeQueries($caller);
    }

    /**
     * @param string $caller
     * @throws EntityDeleteException
     * @throws EntityException
     * @throws EntityFieldException
     * @throws Throwable
     */
    final public function delete(string $caller = Visibility::CALLER_PHP): void
    {
        if ($this->id) {
            $this->_delete($caller);
        }
    }

    /**
     * @param Condition|null $condition
     * @param Order|null $order
     * @param Pager|null $pager
     * @param string $lock
     * @param string $caller
     * @return static[]
     * @throws EntityFieldException
     * @throws ReflectionException
     */
    final public static function getMany(?Condition $condition = null, ?Order $order = null, ?Pager $pager = null, string $lock = EntityLock::DEFAULT, string $caller = Visibility::CALLER_PHP): array
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
        return static::_getMany($condition, $order, $pager, $lock, $caller);
    }

    /**
     * @param Condition|null $condition
     * @param Order|null $order
     * @param Pager|null $pager
     * @param string $caller
     * @return int
     * @throws EntityFieldException
     */
    final public static function count(?Condition $condition = null, ?Order $order = null, ?Pager $pager = null, string $caller = Visibility::CALLER_PHP): int
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
        return static::_count($condition, $order, $pager, $caller);
    }

    /**
     * @param Condition|null $condition
     * @param Order|null $order
     * @param Pager|null $pager
     * @param string $caller
     * @return int
     * @throws EntityFieldException
     */
    final public static function deleteMany(?Condition $condition = null, ?Order $order = null, ?Pager $pager = null, string $caller = Visibility::CALLER_PHP): int
    {
        if ($condition === null) {
            $condition = Condition::falseLeaf();
        }
        if ($order === null) {
            $order = Order::noOrder();
        }
        if ($pager === null) {
            $pager = Pager::unlimited();
        }
        return static::_deleteMany($condition, $order, $pager, $caller);
    }

    /**
     * @param PDOException $e
     * @param string $query
     * @throws EntityFieldException
     */
    protected static function handlePDOException(PDOException $e, string $query): void
    {
        static::_handlePDOException($e, $query);
    }

    ####################################################################################################################
    # Predefined Methods (to wrap in overridable methods) ##############################################################
    ####################################################################################################################
    /**
     *
     */
    final protected function _x_initialize(): void
    {
        foreach (static::fieldSignatures() as $key => $signature) {
            $this->_x_values[self::FIELD_PREFIX][$key] = $signature->default;
            $this->_x_must_fetch[$key] = true;
            $this->_x_must_store[$key] = false;
            if (is_a($signature->declaration, PhpVirtualField::class) or is_a($signature->declaration, DatabaseVirtualField::class)) {
                $this->_x_fetched_by_p[$key] = [];
            }
        }
    }

    /**
     * @param Condition $condition
     * @param Order $order
     * @param string $lock
     * @return static
     * @throws EntityFieldException
     * @throws ReflectionException
     */
    final protected static function _x_getOne(Condition $condition, Order $order, string $lock): static
    {
        return static::_x_getMany($condition, $order, new Pager(1, 0), $lock)[0] ?? new static();
    }

    /**
     * @throws EntityException
     * @throws EntityFieldException
     * @throws Throwable
     */
    final protected function _x_store(): void
    {
        (new EntityBuffer())->add($this)->store();
    }

    /**
     * @return array
     * @throws EntityConditionException
     * @throws EntityException
     * @throws EntityFieldException
     * @throws SuperMarshalException
     * @throws SuperValidationException
     */
    final protected function _x_storeQueries(): array
    {
        $this->validation();

        $array = $this->toDbArray();

        $queries = [];

        // this entity
        if ($this->_x_values[self::FIELD_PREFIX]['id'] === null) {
            if ($this->givenId()) {
                throw (new EntityException())->setError('id', static::class . ' with id ' . $this->givenId() . ' does not exist, use 0 to insert.');
            }

            $query = Query::insert(static::table(), $array);
            static::execute($query->query, $query->bind);

            $this->_x_values[self::FIELD_PREFIX]['id'] = Entity::connection()->lastInsertId();
            $this->_x_must_fetch['id'] = false;
            $this->_x_must_store['id'] = false;
        } else {
            if ($array) {
                $queries[] = Query::update(static::table(), $array, Condition::leaf(CF::_(static::id), Condition::EQ, $this->_x_values[self::FIELD_PREFIX]['id']));
            }
        }

        // related entities
        foreach (static::fieldSignatures() as $key => $signature) {
            $value = $this->_x_values[self::FIELD_PREFIX][$key];
            if (!is_a($signature->declaration, EntityRelation::class) or !$this->_x_must_store[$key]) {
                continue;
            } elseif ($signature->declaration->is11 and $signature->declaration->definedThere) {
                $value->_x_values[$signature->declaration->invName] = $this;
                try {
                    $queries = array_merge($queries, $value->storeQueries());
                } catch (EntityFieldException $e) {
                    throw (new EntityFieldException())->setError($key, $e->getErrors());
                }
            } elseif ($signature->declaration->is1N) {
                foreach ($this->_x_values[self::FIELD_PREFIX][$key] as $relatedEntityKey => $relatedEntity) {
                    $relatedEntity->_x_values[$signature->declaration->invName] = $this;
                    try {
                        $queries = array_merge($queries, $relatedEntity->storeQueries());
                    } catch (EntityFieldException $e) {
                        throw (new EntityFieldException())->setError($key, [$relatedEntityKey => $e->getErrors()]);
                    }
                }
                $removingIds = $this->getAddingRemovingIds($key)[1];
                if ($removingIds) {
                    if ($signature->declaration->invOptional) {
                        $queries[] = Query::update(
                            $signature->declaration->relatedEntity::table(),
                            [$signature->declaration->invName => null],
                            Condition::leaf(CF::_($signature->declaration->relatedEntity::id), Condition::IN, $removingIds)
                        );
                    } else {
                        $queries[] = Query::delete(
                            $signature->declaration->relatedEntity::table(),
                            Condition::leaf($signature->declaration->relatedEntity::table()::C_id(), Condition::IN, $removingIds)
                        );
                    }
                }
            } elseif ($signature->declaration->isNN) {
                foreach ($this->_x_values[self::FIELD_PREFIX][$key] as $relatedEntityKey => $relatedEntity) {
                    try {
                        $queries = array_merge($queries, $relatedEntity->storeQueries());
                    } catch (EntityFieldException $e) {
                        throw (new EntityFieldException())->setError($key, [$relatedEntityKey => $e->getErrors()]);
                    }
                }
                [$addingIds, $removingIds] = $this->getAddingRemovingIds($key);
                $leftColumn = static::table();
                $rightColumn = $signature->declaration->relatedEntity::table();
                if ($addingIds) {
                    $queries[] = Query::insertMany(
                        static::junctionTableName($key),
                        [$leftColumn, $rightColumn],
                        array_map(function ($addingId) { return [$this->_x_values[self::FIELD_PREFIX]['id'], $addingId]; }, $addingIds)
                    );
                }
                if ($removingIds) {
                    $queries[] = Query::delete(
                        static::junctionTableName($key),
                        Condition::rawLeaf("`$leftColumn` = ?", [$this->_x_values[self::FIELD_PREFIX]['id']])
                            ->andR("`$rightColumn` IN (?)", [$removingIds])
                    );
                }
            }
        }

        return $queries;
    }

    /**
     * @throws EntityDeleteException
     * @throws EntityException
     * @throws EntityFieldException
     * @throws Throwable
     */
    final protected function _x_delete(): void
    {
        // @TODO use buffer
        foreach (static::fieldSignatures() as $key => $signature) {
            /** @var Signature $signature */
            if (is_a($signature->declaration, EntityRelation::class) and $signature->declaration->fromOne and $signature->declaration->invRequired and $this->$key) {
                throw new EntityDeleteException("Cannot delete " . static::table() . " because there exists a $key but the inverse nullable is false.");
            }
        }

        foreach (static::fieldSignatures() as $key => $signature) {
            if (is_a($signature->declaration, EntityRelation::class)) {
                if ($signature->declaration->columnThere) {
                    if ($this->$key) {
                        $this->$key->{$signature->declaration->invName} = null;
                        $this->$key->store();
                    }
                } elseif ($signature->declaration->hasJunction) {
                    $this->$key = [];
                    $this->store();
                }
            }
        }

        self::execute("DELETE FROM `" . static::table() . "` WHERE `id` = ? LIMIT 1", [$this->id]);
    }

    /**
     * @param Condition $condition
     * @param Order $order
     * @param Pager $pager
     * @param string $lock
     * @return static[]
     * @throws EntityFieldException
     * @throws ReflectionException
     */
    final protected static function _x_getMany(Condition $condition, Order $order, Pager $pager, string $lock): array
    {
        [$columnsExpression, $keys] = self::columnsExpression();
        $statement = self::execute("SELECT $columnsExpression FROM `" . static::table() . "` " . $condition->joins() . " WHERE $condition->template " . $order->render() . $pager->render() . " " . $lock, $condition->parameters);
        $rawArrays = $statement->fetchAll(PDO::FETCH_NUM);
        $arrays = [];
        foreach ($rawArrays as $item => $rawArray) {
            $arrays[$item] = [];
            foreach ($keys as $i => $key) {
                $arrays[$item][$key] = $rawArray[$i];
            }
        }
        $entityClass = new ReflectionClass(static::class);
        $entities = [];
        foreach ($arrays as $array) {
            /** @var static $entity */
            $entity = $entityClass->newInstanceWithoutConstructor();
            $entities[] = $entity->initialize()->fromDbArray($array);
        }

        return $entities;
    }

    /**
     * @param Condition $condition
     * @param Order $order
     * @param Pager $pager
     * @return int
     * @throws EntityFieldException
     */
    final protected static function _x_count(Condition $condition, Order $order, Pager $pager): int
    {
        $statement = self::execute("SELECT COUNT(`" . self::table() . "`.`id`) as `c` FROM `" . static::table() . "` " . $condition->joins() . " WHERE $condition->template " . $order->render() . $pager->render(), $condition->parameters);
        return $statement->fetch(PDO::FETCH_ASSOC)['c'];
    }

    /**
     * @param Condition $condition
     * @param Order $order
     * @param Pager $pager
     * @return int
     * @throws EntityFieldException
     */
    final protected static function _x_deleteMany(Condition $condition, Order $order, Pager $pager): int
    {
        // @TODO remove relatives or raise error, just like delete
        return self::execute("DELETE FROM `" . static::table() . "` " . $condition->joins() . " WHERE $condition->template " . $order->render() . $pager->render(), $condition->parameters)->rowCount();
    }

    /**
     * @param PDOException $e
     * @param $query
     * @throws EntityFieldException
     */
    final protected static function _x_handlePDOException(PDOException $e, $query): void
    {
        if (str_contains($e->getMessage(), 'Duplicate entry')) {
            $pattern = "/Duplicate entry '([^']*)' for key '([^.]*)\.([^']*)'/";
            preg_match($pattern, $e->getMessage(), $matches);
            $duplicateValues = explode('-', $matches[1]);
            $table = $matches[2];
            $duplicateIndexName = $matches[3];
            $entity = str_replace('_', '\\', $table);
            $duplicateIndexes = array_filter($entity::indexSignatures(), function (Signature $signature) use($duplicateIndexName) {
                return $signature->declaration->name == $duplicateIndexName;
            });
            $duplicateIndex = array_pop($duplicateIndexes);
            $duplicateExpressions = [];
            $iterator = 0;
            $fieldNames = array_keys($duplicateIndex->declaration->fields);
            foreach ($fieldNames as $fieldName) {
                $duplicateExpressions[] = ExpressionService::get('xua.eves.entity.column_equal_to_value', [
                    'column' => ExpressionService::get("column_name.$table.$fieldName"),
                    'value' => $duplicateValues[$iterator],
                ]);
                $iterator++;
            }
            throw (new EntityFieldException())->setError($fieldNames[0], ExpressionService::get('xua.eves.entity.error_message.an_entity_with_expression_already_exists', [
                'entity' => ExpressionService::get('table_name.' . $table),
                'expression' => $duplicateExpressions,
            ]));
        } else {
            throw new PDOException($e->getMessage() . PHP_EOL . $query, 0, $e);
        }
    }

    ####################################################################################################################
    # Predefined Methods (Array-Entity Conversations) ##################################################################
    ####################################################################################################################
    /**
     * @param array $array
     * @return $this
     */
    final protected function fromDbArray(array $array): Entity {
        foreach ($array as $key => $value) {
            $signature = static::signature($key);
            if (is_a($signature->declaration, EntityRelation::class)) {
                if ($signature->declaration->toOne) {
                    $result = new $signature->declaration->relatedEntity($value);
                    if ($result->id) {
                        if ($signature->declaration->fromOne) {
                            $result->_x_values[$signature->declaration->invName] = $this;
                            $result->_x_must_fetch[$signature->declaration->invName] = false;
                            $result->_x_must_store[$signature->declaration->invName] = false;
                        }
                    }
                } else {
                    $result = [];
                    if ($value) {
                        foreach ($value as $id) {
                            $tmp = new $signature->declaration->relatedEntity($id);
                            if ($tmp->id) {
                                if ($signature->declaration->fromOne) {
                                    $tmp->_x_values[$signature->declaration->invName] = $this;
                                    $tmp->_x_must_fetch[$signature->declaration->invName] = false;
                                    $tmp->_x_must_store[$signature->declaration->invName] = false;
                                }
                                $result[] = $tmp;
                            }
                        }
                    }
                }
            } elseif (is_a($signature->declaration, PhpVirtualField::class) or is_a($signature->declaration, DatabaseVirtualField::class)) {
                $this->_x_fetched_by_p[$key] = $signature->p();
                $result = $value;
            } else {
                $result = $signature->declaration->unmarshalDatabase($value);
            }
            $this->_x_values[self::FIELD_PREFIX][$key] = $result;
            $this->_x_must_fetch[$key] = false;
            $this->_x_must_store[$key] = false;
        }

        return $this;
    }

    /**
     * @return array
     * @throws SuperMarshalException
     */
    final protected function toDbArray(): array {
        $array = [];
        foreach (static::fieldSignatures() as $key => $signature) {
            if ($this->_x_must_store[$key] /* @TODO is necessary and $key != 'id' */ and $signature->declaration->databaseType() != 'DONT STORE') {
                $array[$key] = $signature->declaration->marshalDatabase($this->_x_values[self::FIELD_PREFIX][$key]);
            }
        }
        return $array;
    }

    # Predefined Methods (low-level direct db communicator)

    /**
     * @param string|null $fieldName
     * @throws EntityFieldException
     */
    private function _x_fetch(?string $fieldName = 'id') : void
    {
        if (!($this->_x_values[self::FIELD_PREFIX]['id'] ?? false)) {
            return;
        }

        $signature = static::signature($fieldName);
        $array = [];

        if (
            is_a($signature->declaration, EntityRelation::class) and
            $signature->declaration->toMany
        ) {
            if ($signature->declaration->is1N) {
                $statement = self::execute("SELECT id FROM `" . $signature->declaration->relatedEntity::table() . "` WHERE `" . $signature->declaration->invName . "` = ?", [$this->_x_values[self::FIELD_PREFIX]['id']]);
                $rawArray = $statement->fetchAll(PDO::FETCH_NUM);
                if ($rawArray) {
                    $array[$fieldName] = [];
                    foreach ($rawArray as $item) {
                        $array[$fieldName][] = $item[0];
                    }
                }
            } elseif ($signature->declaration->isNN) {
                $statement = self::execute("SELECT `" . $signature->declaration->relatedEntity::table() . "` FROM `" . static::junctionTableName($fieldName) . "` WHERE `" . static::table() . "` = ?", [$this->_x_values[self::FIELD_PREFIX]['id']]);
                $rawArray = $statement->fetchAll(PDO::FETCH_NUM);
                if ($rawArray) {
                    $array[$fieldName] = [];
                    foreach ($rawArray as $item) {
                        $array[$fieldName][] = $item[0];
                    }
                }
            }
        } elseif (is_a($signature->declaration, PhpVirtualField::class)) {
            $array[$fieldName] = ($signature->declaration->getter)($this, $signature->p());
        } else {
            [$columnsExpression, $keys] = self::columnsExpression($this);
            if ($columnsExpression) {
                $statement = self::execute("SELECT $columnsExpression FROM `" . static::table() . "` WHERE `" . static::table() . "`.`id` = ? LIMIT 1", [$this->_x_values[self::FIELD_PREFIX]['id']]);
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

    ####################################################################################################################
    # Predefined Methods (helpers) #####################################################################################
    ####################################################################################################################

    /**
     * @return array
     */
    #[ArrayShape(['tableNames' => "array", 'alters' => "string"])]
    final public static function alter(): array
    {
        $tables = [];

        $columns = [];
        foreach (static::fieldSignatures() as $key => $signature) {
            if ($signature->declaration->databaseType() != 'DONT STORE') {
                $columns[$key] = Column::fromQuery("$key {$signature->declaration->databaseType()}");
            }
            if (
                is_a($signature->declaration, EntityRelation::class) and
                $signature->declaration->hasJunction and
                $signature->declaration->definedHere
            ) {
                $leftColumn = static::table();
                $rightColumn = $signature->declaration->relatedEntity::table();
                $tables[] = new TableScheme(static::junctionTableName($key), [
                    $leftColumn => Column::fromQuery(static::table() . ' ' . $signature->declaration->relatedEntity::signature('id')->declaration->databaseType() . " NOT NULL"),
                    $rightColumn => Column::fromQuery($signature->declaration->relatedEntity::table() . ' ' . $signature->declaration->relatedEntity::signature('id')->declaration->databaseType() . " NOT NULL"),
                ], [
                    Signature::new(null, null, null, null, new OrderScheme([
                        OrderScheme::fields => [
                            [
                                OrderScheme::direction => OrderScheme::DIRECTION_ASC,
                                OrderScheme::field => $leftColumn,
                            ],
                            [
                                OrderScheme::direction => OrderScheme::DIRECTION_ASC,
                                OrderScheme::field => $rightColumn,
                            ],
                        ],
                        OrderScheme::unique => true,
                        OrderScheme::name => 'PRIMARY',
                    ])),
                ]);
            }
        }

        $tables[] = new TableScheme(static::table(), $columns, static::indexSignatures());
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

    /**
     * @throws EntityFieldException
     */
    final public static function startTransaction(): void
    {
        static::execute("START TRANSACTION");
    }

    /**
     * @return int
     * @throws EntityFieldException
     */
    final public static function savePoint(): int
    {
        static::execute("SAVEPOINT savepoint" . ++self::$_x_lastSavepointNo);
        return self::$_x_lastSavepointNo;
    }

    /**
     * @param int $savepointNo
     * @throws EntityFieldException
     */
    final public static function rollbackToSavepoint(int $savepointNo): void
    {
        static::execute("ROLLBACK TO savepoint$savepointNo");
    }

    /**
     * @throws EntityFieldException
     */
    final public static function commit(): void
    {
        static::execute("COMMIT");
    }

    /**
     * @throws EntityFieldException
     */
    final public static function rollback(): void
    {
        static::execute("ROLLBACK");
    }

    /**
     * @param Entity|null $entity
     * @return array
     */
    private static function columnsExpression(?Entity $entity = null): array
    {
        $columnExpressions = [];
        $keys = [];
        foreach (static::fieldSignatures() as $key => $signature) {
            /** @var Signature $signature */
            if ($entity and !$entity->_x_must_fetch[$key]) {
                continue;
            }
            if (is_a($signature->declaration, DatabaseVirtualField::class)) {
                $expression =  ($signature->declaration->getter)($signature->p()) . ' `' . $signature->name . '`';
            } elseif ($signature->declaration->databaseType() != 'DONT STORE') {
                $expression = '`' . static::table() . '`.`' . $signature->name . '`';
            } else {
                continue;
            }

            $columnExpressions[] = $expression;
            $keys[] = $key;
        }

        $columnsExpression = implode(', ', $columnExpressions);

        return [$columnsExpression, $keys];
    }

    /**
     * @param string $fieldName
     * @return string
     */
    final public static function junctionTableName (string $fieldName): string
    {
        $signature = static::signature($fieldName);
        /** @var EntityRelation $type */
        $type = $signature->declaration;
        return $type->definedHere
            ? '_' . static::table() . '_' . $fieldName
            : '_' . $type->relatedEntity::table() . '_' . $type->invName;
    }

    /**
     * @param Entity $entity
     * @param string $key
     */
    private function addThisToAnotherEntity (Entity $entity, string $key) {
        // one-to-? relation
        if ($entity->_x_values[self::FIELD_PREFIX][$key] === null or $entity->_x_values[self::FIELD_PREFIX][$key] instanceof Entity) {
            if ($entity->_x_values[self::FIELD_PREFIX][$key] === null or $entity->_x_values[self::FIELD_PREFIX][$key] !== $this) {
                $entity->_x_values[self::FIELD_PREFIX][$key] = $this;
                $entity->_x_must_fetch[$key] = false;
                $entity->_x_must_store[$key] = true;
            }
        }
        // many-to-? relation
        else {
            $found = false;
            foreach ($entity->_x_values[self::FIELD_PREFIX][$key] as $index => $item) {
                if ($item->_x_values['id'] == $this->_x_values[self::FIELD_PREFIX]['id']) {
                    $entity->_x_values[self::FIELD_PREFIX][$key][$index] = $this;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $entity->_x_values[self::FIELD_PREFIX][$key][] = $this;
            }
            $entity->_x_must_fetch[$key] = false;
            $entity->_x_must_store[$key] = true;
        }
    }

    /**
     * @param string $key
     * @return array
     * @throws EntityFieldException
     */
    private function getAddingRemovingIds(string $key): array
    {
        $currentIds = array_map(function (Entity $entity) {
            return $entity->_x_values[self::FIELD_PREFIX]['id'];
        }, $this->_x_values[self::FIELD_PREFIX][$key]);

        $dbIds = array_map(function (Entity $entity) {
            return $entity->_x_values[self::FIELD_PREFIX]['id'];
        }, (new static($this->_x_values[self::FIELD_PREFIX]['id']))->$key);

        $addingIds = array_diff($currentIds, $dbIds);
        $removingIds = array_diff($dbIds, $currentIds);
        return [$addingIds, $removingIds];
    }
}