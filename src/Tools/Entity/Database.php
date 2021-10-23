<?php

namespace Xua\Core\Tools\Entity;

final class Database
{
    const DATABASE_ENGINE_MYSQL = 'mysql';
    const DATABASE_ENGINE_ = [
        self::DATABASE_ENGINE_MYSQL
    ];

    const TRANSACTION_ISOLATION_LEVEL_READ_UNCOMMITTED = 'READ-UNCOMMITTED';
    const TRANSACTION_ISOLATION_LEVEL_READ_COMMITTED = 'READ-COMMITTED';
    const TRANSACTION_ISOLATION_LEVEL_REPEATABLE_READ = 'REPEATABLE-READ';
    const TRANSACTION_ISOLATION_LEVEL_SERIALIZABLE = 'SERIALIZABLE';
    const TRANSACTION_ISOLATION_LEVEL_ = [
        self::TRANSACTION_ISOLATION_LEVEL_READ_UNCOMMITTED,
        self::TRANSACTION_ISOLATION_LEVEL_READ_COMMITTED,
        self::TRANSACTION_ISOLATION_LEVEL_REPEATABLE_READ,
        self::TRANSACTION_ISOLATION_LEVEL_SERIALIZABLE,
    ];

    const TRANSACTION_ISOLATION_LEVEL_MAPPING = [
        self::TRANSACTION_ISOLATION_LEVEL_READ_UNCOMMITTED => 'READ UNCOMMITTED',
        self::TRANSACTION_ISOLATION_LEVEL_READ_COMMITTED => 'READ COMMITTED',
        self::TRANSACTION_ISOLATION_LEVEL_REPEATABLE_READ => 'REPEATABLE READ',
        self::TRANSACTION_ISOLATION_LEVEL_SERIALIZABLE => 'SERIALIZABLE',
    ];

    public static function transactionIsolationLevel(string $level): string
    {
        return self::TRANSACTION_ISOLATION_LEVEL_MAPPING[$level];
    }
}