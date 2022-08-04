<?php

namespace Xua\Core\Eves;

use PDO;
use PDOException;
use PDOStatement;
use Xua\Core\Exceptions\NotImplementedException;
use Xua\Core\Exceptions\SuperMarshalException;
use Xua\Core\Services\ConstantService;
use Xua\Core\Services\DateTimeInstance;
use Xua\Core\Services\EnvironmentService;
use Xua\Core\Services\ExpressionService;
use Xua\Core\Services\JsonLogService;
use Xua\Core\Services\LocaleLanguage;
use Xua\Core\Supers\Special\DatabaseVirtualField;
use Xua\Core\Supers\Special\EntityRelation;
use Xua\Core\Supers\Special\OrderScheme;
use Xua\Core\Supers\Special\PhpVirtualField;
use Xua\Core\Exceptions\EntityException;
use Xua\Core\Exceptions\MagicCallException;
use Xua\Core\Exceptions\EntityDeleteException;
use Xua\Core\Exceptions\EntityFieldException;
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

// @TODO this file needs some serious refactoring and cleanings.

/**
 * @property mixed id
 */
abstract class Entity extends Block
{
    const id = self::class . '::id';

    const JUNCTION_LEFT = 'left';
    const JUNCTION_RIGHT = 'right';
    ####################################################################################################################
    # Database Engine Connection #######################################################################################
    ####################################################################################################################
    /**
     * @var PDO|null
     */
    private static ?PDO $connection = null;

    private static bool $_x_transaction_started = false;

    // @TODO remove usages of connection to force all queries pass the channel  Entity::execute(...)
    /**
     * @return PDO|null
     */
    final public static function connection(): ?PDO
    {
        if (!self::$connection) {
            $dbInfo = ConstantService::get('config', 'db');
            if (!$dbInfo) {
                throw new EntityException('Database connection config not found.');
            }
//            Dialect::$engine = $dbInfo['engine'];
            $dbInfo['dsn'] = $dbInfo['engine'] . ":host=" . $dbInfo['hostname'] . ";port=" . $dbInfo['port']  . ";dbname=" . $dbInfo['database'];
            self::$connection = new PDO($dbInfo['dsn'], $dbInfo['username'], $dbInfo['password'], [
                PDO::ATTR_TIMEOUT => $dbInfo['timeout'] ?? 10,
            ]);
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::startTransaction();
            self::$_x_transaction_started = true;
        }
        return self::$connection;
    }

    final public static function execute(string $query, array $bind = []): false|PDOStatement
    {
        [$query, $bind] = QueryBinder::getQueryAndBind($query, $bind);
        self::executeLog($query, $bind);
        try {
            $statement = self::connection()->prepare($query);
            $statement->execute($bind);
            return $statement;
        } catch (PDOException $e) {
            static::handlePDOException($e, QueryBinder::bind($query, $bind));
            return false;
        }
    }

    final public static function executeLog(string $query, array $bind): void
    {
        if (in_array(EnvironmentService::env(), ConstantService::get('config', 'services.entity.logEnvs'))) {
            JsonLogService::append('entity', [
                'time' => (new DateTimeInstance())->format('Y/m/d-H:i:s', null, LocaleLanguage::LANG_EN, LocaleLanguage::CAL_GREGORIAN),
                'query' => QueryBinder::bind($query, $bind),
                'trace' => implode(PHP_EOL, array_map(
                    function (array $traceItem): string {
                        return ($traceItem['file'] ?? '') . ':' . ($traceItem['line'] ?? '');
                    },
                    debug_backtrace()
                ))
            ]);
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
     * @var Entity[]
     */
    private static array $_x_cached_by_id = [];

    /**
     * @var bool[]
     */
    private static array $_x_entities_visited_for_store = [];

    final public static function _init(): void
    {
        parent::_init();
        self::$_x_table[static::class] = implode('_', explode("\\", static::class));
    }

    /**
     * @param int|null $id
     */
    final private function __construct(?int $id = 0)
    {
        $this->initialize();
        $this->_x_given_id = $id;
    }

    final public static function new(?int $id = 0): static
    {
        if(!isset(self::$_x_cached_by_id[static::class])) {
            self::$_x_cached_by_id[static::class] = [];
        }

        if ($id) {
            if (!isset(self::$_x_cached_by_id[static::class][$id])) {
                $statement = self::execute("SELECT EXISTS (SELECT * FROM `" . static::table() . "` WHERE `id` = ?) e", [$id]);
                if ($statement->fetch()['e']) {
                    $newEntity = new static($id);
                    $newEntity->_x_values[self::FIELD_PREFIX]['id'] = $id;
                    $newEntity->_x_must_fetch['id'] = false;
                    $newEntity->_x_must_store['id'] = false;
                    self::$_x_cached_by_id[static::class][$id] = $newEntity;
                }
            }
            if (isset(self::$_x_cached_by_id[static::class][$id])) {
                return self::$_x_cached_by_id[static::class][$id];
            }
        }

        $newEntity = new static($id);
        foreach (static::fieldSignatures() as $key => $signature) {
            $newEntity->_x_must_store[$key] = true;
        }
        $newEntity->_x_must_fetch['id'] = false;
        $newEntity->_x_must_store['id'] = false;

        return $newEntity;
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
     * @return void
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
     */
    final protected function setterProcedure(string $prefix, string $name, Signature $signature, mixed $value): void
    {
        if ($name == 'id') {
            throw (new MagicCallException())->setError($name, 'Cannot change id of an entity.');
        }

        if (is_a($signature->declaration, PhpVirtualField::class)) {
            if ($signature->declaration->setter !== null) {
                ($signature->declaration->setter)($this, $value, $signature->p());
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

        $oldValue = $this->_x_values[self::FIELD_PREFIX][$name];
        if (
            is_object($oldValue) or
            (is_array($oldValue) and $oldValue and is_object($oldValue[array_key_first($oldValue)])) or
            $oldValue != $value or
            $this->_x_must_fetch[$name]
        ) {
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
                            OrderScheme::field => $signature,
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
                        OrderScheme::field => Signature::_(static::id),
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
    protected function _validation(EntityFieldException $exception): void
    {
        // Empty by default
    }

    protected function _initialize(): void
    {
        $this->_x_initialize();
    }

    /**
     * @noinspection PhpUnusedParameterInspection
     */
    protected static function _getOne(Condition $condition, Order $order, string $lock, string $caller): static
    {
        return static::_x_getOne($condition, $order, $lock);
    }

    /**
     * @noinspection PhpUnusedParameterInspection
     */
    protected function _store(string $caller): void
    {
        $this->_x_store();
    }

    /**
     * @return Query[]
     * @noinspection PhpUnusedParameterInspection
     */
    protected function _storeQueries(string $caller): array
    {
        return $this->_x_storeQueries();
    }

    /**
     * @noinspection PhpUnusedParameterInspection
     */
    protected function _delete(bool $force, string $caller): void
    {
        $this->_x_delete($force);
    }

    /**
     * @return Query[]
     * @noinspection PhpUnusedParameterInspection
     */
    protected function _deleteQueries(bool $force, string $caller): array
    {
        return $this->_x_deleteQueries($force);
    }

    /**
     * @return static[]
     * @noinspection PhpUnusedParameterInspection
     */
    protected static function _getMany(Condition $condition, Order $order, Pager $pager, string $lock, string $caller): array
    {
        return static::_x_getMany($condition, $order, $pager, $lock);
    }

    /**
     * @noinspection PhpUnusedParameterInspection
     */
    protected static function _count(Condition $condition, Order $order, Pager $pager, string $caller): int
    {
        return static::_x_count($condition, $order, $pager);
    }

    /**
     * @noinspection PhpUnusedParameterInspection
     */
    protected static function _deleteMany(Condition $condition, Order $order, Pager $pager, string $caller): int
    {
        return static::_x_deleteMany($condition, $order, $pager);
    }

    /**
     * @return Query[]
     * @noinspection PhpUnusedParameterInspection
     */
    protected static function _deleteManyQueries(Condition $condition, Order $order, Pager $pager, string $caller): array
    {
        return static::_x_deleteManyQueries($condition, $order, $pager);
    }

    protected static function _handlePDOException(PDOException $e, string $query): void
    {
        static::_x_handlePDOException($e, $query);
    }

    /**
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
    public function validation(): void
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
     * @return $this
     */
    final public function store(string $caller = Visibility::CALLER_PHP): static
    {
        $this->_store($caller);
        return $this;
    }

    /**
     * @return Query[]
     */
    final protected function storeQueries(string $caller = Visibility::CALLER_PHP): array
    {
        return $this->_storeQueries($caller);
    }

    final public function delete(bool $force = false, string $caller = Visibility::CALLER_PHP): void
    {
        $this->_delete($force, $caller);
    }

    /**
     * @return Query[]
     */
    final public function deleteQueries(bool $force = false, string $caller = Visibility::CALLER_PHP): array
    {
        return $this->id ? $this->_deleteQueries($force, $caller) : [];
    }

    /**
     * @return static[]
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
     * @return Query[]
     */
    final public static function deleteManyQueries(?Condition $condition = null, ?Order $order = null, ?Pager $pager = null, string $caller = Visibility::CALLER_PHP): array
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
        return static::_deleteManyQueries($condition, $order, $pager, $caller);
    }

    /**
     * @param PDOException $e
     * @param string $query
     */
    protected static function handlePDOException(PDOException $e, string $query): void
    {
        static::_handlePDOException($e, $query);
    }

    ####################################################################################################################
    # Predefined Methods (to wrap in overridable methods) ##############################################################
    ####################################################################################################################
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

    final protected static function _x_getOne(Condition $condition, Order $order, string $lock): static
    {
        return static::_x_getMany($condition, $order, new Pager(1, 0), $lock)[0] ?? static::new();
    }

    final protected function _x_store(): void
    {
        (new EntityBuffer())->add($this)->store();
    }

    /**
     * @return Query[]
     */
    final protected function _x_storeQueries(): array
    {
        if (self::$_x_entities_visited_for_store[spl_object_hash($this)] ?? false) {
            return [];
        }

        self::$_x_entities_visited_for_store[spl_object_hash($this)] = true;

        $this->validation();

        $array = $this->toDbArray();

        $queries = [];

        // inserts for related entities
        $signatures = static::fieldSignatures();
        foreach ($signatures as $key => $signature) {
            if (!$this->_x_must_store[$key]) {
                continue;
            }
            $value = $this->_x_values[self::FIELD_PREFIX][$key];
            if (
                $value and
                is_a($signature->declaration, EntityRelation::class) and
                (
                    ($signature->declaration->is11 and $signature->declaration->definedHere) or
                    $signature->declaration->isN1
                    // @TODO is there other cases?
                )
            ) {
                $shouldInsert = !$value->id;
                $queries = array_merge($queries, $value->storeQueries());
                if ($shouldInsert) {
                    $array[$key] = $value->id;
                }
            }
        }

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
            self::$_x_cached_by_id[static::class][$this->_x_values[self::FIELD_PREFIX]['id']] = $this;
        } else {
            if ($array) {
                $queries[] = Query::update(static::table(), $array, Condition::leaf(CF::_(static::id), Condition::EQ, $this->_x_values[self::FIELD_PREFIX]['id']));
            }
        }

        // related entities
        foreach ($signatures as $key => $signature) {
            if (!$this->_x_must_store[$key]) {
                continue;
            }
            $value = $this->_x_values[self::FIELD_PREFIX][$key];
            if (!is_a($signature->declaration, EntityRelation::class) or ($this->_x_must_fetch[$key] and !$this->_x_must_store[$key])) {
                continue;
            } elseif ($signature->declaration->is11 and $signature->declaration->definedThere) {
                $value->_x_values[self::FIELD_PREFIX][$signature->declaration->invName] = $this;
                try {
                    $queries = array_merge($queries, $value->storeQueries());
                } catch (EntityFieldException $e) {
                    throw (new EntityFieldException())->setError($key, $e->getErrors());
                }
                if ($signature->declaration->definedHere) {
                    $this->_x_values[self::FIELD_PREFIX][$key] = $value;
                }
            } elseif ($signature->declaration->is1N) {
                foreach ($this->_x_values[self::FIELD_PREFIX][$key] as $relatedEntityKey => $relatedEntity) {
                    $relatedEntity->_x_values[self::FIELD_PREFIX][$signature->declaration->invName] = $this;
                    try {
                        $queries = array_merge($queries, $relatedEntity->storeQueries());
                    } catch (EntityFieldException $e) {
                        throw (new EntityFieldException())->setError($key, [$relatedEntityKey => $e->getErrors()]);
                    }
                }
                $removingIds = $this->getAddingRemovingIds($key)[1];
                if ($removingIds) {
                    if ($signature->declaration->invNullable) {
                        $queries[] = Query::update(
                            $signature->declaration->relatedEntity::table(),
                            [$signature->declaration->invName => null],
                            Condition::leaf(CF::_($signature->declaration->relatedEntity::id), Condition::IN, $removingIds)
                        );
                    } else {
                        $queries = array_merge($queries, $signature->declaration->relatedEntity::deleteManyQueries(
                            Condition::leaf(CF::_($signature->declaration->relatedEntity::id), Condition::IN, $removingIds)
                        ));
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
                if ($addingIds) {
                    $queries[] = Query::insertMany(
                        static::junctionTableName($key),
                        [self::JUNCTION_LEFT, self::JUNCTION_RIGHT],
                        array_map(function ($addingId) { return [$this->_x_values[self::FIELD_PREFIX]['id'], $addingId]; }, $addingIds)
                    );
                }
                if ($removingIds) {
                    $queries[] = Query::delete(
                        static::junctionTableName($key),
                        Condition::rawLeaf('`' . self::JUNCTION_LEFT . '` = ?', [$this->_x_values[self::FIELD_PREFIX]['id']])
                            ->andR('`' . self::JUNCTION_RIGHT . '` IN (?)', [$removingIds])
                    );
                }
            }
        }

        return $queries;
    }

    final protected function _x_delete(bool $force): void
    {
        // @TODO remove from _x_cached_by_id
        $queryString = '';
        $bind = [];
        foreach ($this->deleteQueries($force) as $query) {
            $queryString .= $query->query . ';';
            $bind = array_merge($bind, $query->bind);
        }
        if ($queryString) {
            self::execute($queryString, $bind);
        }
    }

    /**
     * @return Query[]
     */
    final protected function _x_deleteQueries(bool $force): array
    {
        $queries = [];

        foreach (static::fieldSignatures() as $key => $signature) {
            /** @var Signature $signature */
            if (is_a($signature->declaration, EntityRelation::class) and $signature->declaration->invRequired and $this->$key) {
                if ($force) {
                    $queries = array_merge($queries, $this->$key->deleteQueries());
                }
                else {
                    throw new EntityDeleteException("Cannot delete " . static::table() . " because there exists a $key but the inverse nullable is false.");
                }
            }
        }

        foreach (static::fieldSignatures() as $key => $signature) {
            if (is_a($signature->declaration, EntityRelation::class)) {
                if ($signature->declaration->columnThere) {
                    if ($this->$key and $signature->declaration->invName !== null) {
                        $this->$key->{$signature->declaration->invName} = null;
                        $this->$key->store();
                    }
                } elseif ($signature->declaration->hasJunction) {
                    $this->$key = [];
                    $this->store();
                }
            }
        }

        $queries[] = Query::delete(static::table(), Condition::leaf(CF::_(static::id), Condition::EQ, $this->id));

        return $queries;
    }

    /**
     * @return static[]
     */
    final protected static function _x_getMany(Condition $condition, Order $order, Pager $pager, string $lock): array
    {
        [$columnsExpression, $keys] = self::columnsExpression();
        $statement = self::execute("SELECT DISTINCT $columnsExpression FROM `" . static::table() . "` " . $condition->joins() . " WHERE $condition->template " . $order->render() . " " . $pager->render() . " " . $lock, $condition->parameters);
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
            if (!isset(self::$_x_cached_by_id[static::class][$array['id']])) {
                self::$_x_cached_by_id[static::class][$array['id']] = (new static($array['id']))->fromDbArray($array);
            }
            $entities[] = self::$_x_cached_by_id[static::class][$array['id']];
        }

        return $entities;
    }

    final protected static function _x_count(Condition $condition, Order $order, Pager $pager): int
    {
        $statement = self::execute("SELECT COUNT(`" . self::table() . "`.`id`) as `c` FROM `" . static::table() . "` " . $condition->joins() . " WHERE $condition->template " . $order->render() . " " . $pager->render(), $condition->parameters);
        return $statement->fetch(PDO::FETCH_ASSOC)['c'];
    }

    final protected static function _x_deleteMany(Condition $condition, Order $order, Pager $pager): int
    {
        // @TODO remove from _x_cached_by_id
        $queryString = '';
        $bind = [];
        foreach (static::deleteManyQueries($condition, $order, $pager) as $query) {
            $queryString .= $query->query . ';';
            $bind = array_merge($bind, $query->bind);
        }
        if ($queryString) {
            return self::execute($queryString, $bind)->rowCount();
        }
        return 0;
    }

    /**
     * @return Query[]
     */
    final protected static function _x_deleteManyQueries(Condition $condition, Order $order, Pager $pager): array
    {
        $queries = [];

//        @TODO remove relatives or raise error, just like delete
//        foreach (static::fieldSignatures() as $key => $signature) {
//            /** @var Signature $signature */
//            if (is_a($signature->declaration, EntityRelation::class) and $signature->declaration->invRequired and $this->$key) {
//                if ($force) {
//                    $queries = array_merge($queries, $this->$key->deleteQueries());
//                }
//                else {
//                    throw new EntityDeleteException("Cannot delete " . static::table() . " because there exists a $key but the inverse nullable is false.");
//                }
//            }
//        }
//
//        foreach (static::fieldSignatures() as $key => $signature) {
//            if (is_a($signature->declaration, EntityRelation::class)) {
//                if ($signature->declaration->columnThere) {
//                    if ($this->$key) {
//                        $this->$key->{$signature->declaration->invName} = null;
//                        $this->$key->store();
//                    }
//                } elseif ($signature->declaration->hasJunction) {
//                    $this->$key = [];
//                    $this->store();
//                }
//            }
//        }

        $queries[] = Query::delete(
            static::table(),
            $condition,
            $order,
            $pager,
        );

        return $queries;
    }

    /**
     * @param PDOException $e
     * @param $query
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
            $fieldNames = array_map(function (array $field) { return $field['field']->name; }, $duplicateIndex->declaration->fields);
            foreach ($fieldNames as $fieldName) {
                $duplicateExpressions[] = ExpressionService::getXua('eves.entity.column_equal_to_value', [
                    'column' => ExpressionService::get("column_name.$table.$fieldName"),
                    'value' => $duplicateValues[$iterator],
                ]);
                $iterator++;
            }
            $message = ExpressionService::getXua('eves.entity.error_message.an_entity_with_expression_already_exists', [
                'entity' => ExpressionService::get('table_name.' . $table),
                'expression' => $duplicateExpressions,
            ]);
            if (LocaleLanguage::getLanguage() == LocaleLanguage::LANG_EN) {
                $message = ucfirst($message);
            }
            throw (new EntityFieldException())->setError($fieldNames[0], $message);
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
                // @TODO better read relations later than fetching the minimal object
                if ($signature->declaration->toOne) {
                    if ($value === null) {
                        $result = null;
                    } else {
                        $result = $signature->declaration->relatedEntity::new($value);
                        if ($result->id) {
                            if ($signature->declaration->fromOne) {
                                $result->_x_values[self::FIELD_PREFIX][$signature->declaration->invName] = $this;
                                $result->_x_must_fetch[$signature->declaration->invName] = false;
                                $result->_x_must_store[$signature->declaration->invName] = false;
                            }
                        }
                    }
                } else {
                    $result = [];
                    if ($value) {
                        foreach ($value as $id) {
                            $tmp = $signature->declaration->relatedEntity::new($id);
                            if ($tmp->id) {
                                if ($signature->declaration->fromOne) {
                                    $tmp->_x_values[self::FIELD_PREFIX][$signature->declaration->invName] = $this;
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

    # Predefined Methods (low-level direct db communication)
    /**
     * @param string|null $fieldName
     */
    private function _x_fetch(?string $fieldName = 'id'): void
    {
        if (!($this->_x_values[self::FIELD_PREFIX]['id'] ?? false)) {
            return;
        }
        $signature = static::signature($fieldName);
        // @TODO think more about it (how to handle relations which are of two types (id is present when fetching efficient, and not present, we should execute another query))
        if (is_a($signature->declaration, EntityRelation::class) and (is_a($signature->declaration, EntityRelation::class) and $signature->declaration->toMany)) {
            $this->_x_fetch_related_entity($signature);
        } elseif (is_a($signature->declaration, PhpVirtualField::class)) {
            $this->_x_fetch_virtual_field($signature);
        } else {
            $this->_x_fetch_efficient();
        }
    }

    private function _x_fetch_related_entity(Signature $signature): void
    {
        $result = [];
        if (
            !is_a($signature->declaration, EntityRelation::class) or
            !$signature->declaration->toMany
        ) {
            return;
        }
        if ($signature->declaration->is1N) {
            $statement = self::execute("SELECT id FROM `" . $signature->declaration->relatedEntity::table() . "` WHERE `" . $signature->declaration->invName . "` = ?", [$this->_x_values[self::FIELD_PREFIX]['id']]);
        } else { // $signature->declaration->isNN
            if ($signature->declaration->definedHere) {
                $here = self::JUNCTION_LEFT;
                $there = self::JUNCTION_RIGHT;
            } else {
                $here = self::JUNCTION_RIGHT;
                $there = self::JUNCTION_LEFT;
            }
            $statement = self::execute("SELECT `$there` FROM `" . static::junctionTableName($signature->name) . "` WHERE `$here` = ?", [$this->_x_values[self::FIELD_PREFIX]['id']]);
        }
        $rawArray = $statement->fetchAll(PDO::FETCH_NUM);
        if (!$rawArray) {
            return;
        }
        foreach ($rawArray as $item) {
            $result[] = $item[0];
        }
        $this->fromDbArray([$signature->name => $result]);
    }

    private function _x_fetch_virtual_field(Signature $signature): void
    {
        $this->fromDbArray([
            $signature->name => ($signature->declaration->getter)($this, $signature->p()),
        ]);
    }

    private function _x_fetch_efficient(): void
    {
        [$columnsExpression, $keys] = self::columnsExpression($this);
        if (!$columnsExpression) {
            return;
        }
        $statement = self::execute("SELECT $columnsExpression FROM `" . static::table() . "` WHERE `" . static::table() . "`.`id` = ? LIMIT 1", [$this->_x_values[self::FIELD_PREFIX]['id']]);
        $rawArray = $statement->fetch(PDO::FETCH_NUM);
        if (!$rawArray) {
            return;
        }
        $array = [];
        foreach ($keys as $i => $key) {
            $array[$key] = $rawArray[$i];
        }
        $this->fromDbArray($array);

    }

    ####################################################################################################################
    # Predefined Methods (helpers) #####################################################################################
    ####################################################################################################################

    /**
     * @return array
     */
    final public static function alter(): array
    {
        $tables = [];

        $columns = [];
        foreach (static::fieldSignatures() as $key => $signature) {
            if ($signature->declaration->databaseType() != 'DONT STORE') {
                if ($signature->default === null) {
                    $default = null;
                } else {
                    try {
                        $default = $signature->declaration->marshalDatabase($signature->default);
                    } catch (SuperMarshalException $exception) {
                        throw (new EntityException)->setError(static::class . '.' . $key, $exception->getMessage());
                    }
                }

                $columns[$key] = Column::fromQuery(
                    $key,
                    $signature->declaration->databaseType(),
                    $default,
                );
            }
            if (
                is_a($signature->declaration, EntityRelation::class) and
                $signature->declaration->hasJunction and
                $signature->declaration->definedHere
            ) {
                $leftSignature = Signature::new(null, static::junctionTableName($key) . '::' . self::JUNCTION_LEFT, null, null, static::signature('id')->declaration);
                $rightSignature = Signature::new(null, static::junctionTableName($key) . '::' . self::JUNCTION_RIGHT, null, null, $signature->declaration->relatedEntity::signature('id')->declaration);
                $tables[] = new TableScheme(static::junctionTableName($key), [
                    self::JUNCTION_LEFT => Column::fromQuery(self::JUNCTION_LEFT, str_replace('AUTO_INCREMENT', '', $leftSignature->declaration->databaseType()) . " NOT NULL"),
                    self::JUNCTION_RIGHT => Column::fromQuery(self::JUNCTION_RIGHT, str_replace('AUTO_INCREMENT', '', $rightSignature->declaration->databaseType()) . " NOT NULL"),
                ], [
                    Signature::new(null, null, null, null, new OrderScheme([
                        OrderScheme::fields => [
                            [
                                OrderScheme::direction => OrderScheme::DIRECTION_ASC,
                                OrderScheme::field => $leftSignature,
                            ],
                            [
                                OrderScheme::direction => OrderScheme::DIRECTION_ASC,
                                OrderScheme::field => $rightSignature,
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

    final public static function startTransaction(): void
    {
        static::execute("START TRANSACTION");
    }

    /**
     * @return int
     */
    final public static function savePoint(): int
    {
        static::execute("SAVEPOINT savepoint" . ++self::$_x_lastSavepointNo);
        return self::$_x_lastSavepointNo;
    }

    /**
     * @param int $savepointNo
     */
    final public static function rollbackToSavepoint(int $savepointNo): void
    {
        static::execute("ROLLBACK TO savepoint$savepointNo");
    }

    final public static function commit(): void
    {
        if (self::$_x_transaction_started) {
            static::execute("COMMIT");
        }
    }

    final public static function rollback(): void
    {
        if (self::$_x_transaction_started) {
            static::execute("ROLLBACK");
        }
    }

    /**
     * @param Entity|null $entity
     * @return array
     */
    private static function columnsExpression(?Entity $entity = null): array
    {
        $columnExpressions = [];
        $keys = [];
        $table = static::table();
        foreach (static::fieldSignatures() as $key => $signature) {
            /** @var Signature $signature */
            if ($entity and !$entity->_x_must_fetch[$key]) {
                continue;
            }
            if (is_a($signature->declaration, DatabaseVirtualField::class)) {
                $databaseFieldExpression = ($signature->declaration->getter)($signature->p());
                $databaseFieldExpression = str_replace('#XuaTableName#', "`$table`", $databaseFieldExpression);
                $expression = "($databaseFieldExpression) `{$table}__$signature->name`";
            } elseif ($signature->declaration->databaseType() != 'DONT STORE') {
                $expression = "`$table`.`$signature->name`";
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
                if ($item->_x_values[self::FIELD_PREFIX]['id'] == $this->_x_values[self::FIELD_PREFIX]['id']) {
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
     */
    private function getAddingRemovingIds(string $key): array
    {
        $currentIds = array_map(function (Entity $entity) {
            return $entity->_x_values[self::FIELD_PREFIX]['id'];
        }, $this->_x_values[self::FIELD_PREFIX][$key]);

        // @TODO better to store db data and local data separately and clean this mess
        $signature = static::signature($key);
        if ($signature->declaration->is1N) {
            $statement = self::execute("SELECT id FROM `" . $signature->declaration->relatedEntity::table() . "` WHERE `" . $signature->declaration->invName . "` = ?", [$this->_x_values[self::FIELD_PREFIX]['id']]);
        } else { // $signature->declaration->isNN
            if ($signature->declaration->definedHere) {
                $here = self::JUNCTION_LEFT;
                $there = self::JUNCTION_RIGHT;
            } else {
                $here = self::JUNCTION_RIGHT;
                $there = self::JUNCTION_LEFT;
            }
            $statement = self::execute("SELECT `$there` FROM `" . static::junctionTableName($signature->name) . "` WHERE `$here` = ?", [$this->_x_values[self::FIELD_PREFIX]['id']]);
        }
        $rawArray = $statement->fetchAll(PDO::FETCH_NUM);
        $dbIds = $rawArray ? array_map(function (array $rawItem) {
            return $rawItem[0];
        }, $rawArray) : [];

        $addingIds = array_values(array_diff($currentIds, $dbIds));
        $removingIds = array_values(array_diff($dbIds, $currentIds));
        return [$addingIds, $removingIds];
    }
}