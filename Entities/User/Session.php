<?php

namespace Entities\User;


use Entities\User;
use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\Highers\DateTime;
use Supers\Basics\Strings\Enum;
use Supers\Basics\Strings\Text;
use XUA\Entity;
use XUA\Tools\Entity\ConditionField;
use XUA\Tools\Signature\EntityFieldSignature;


/**
 * @property int id
 * @method static EntityFieldSignature F_id() The Signature of: Field `id`
 * @method static ConditionField C_id() The Condition Field of: Field `id`
 * @property \Entities\User user
 * @method static EntityFieldSignature F_user() The Signature of: Field `user`
 * @method static ConditionField C_user() The Condition Field of: Field `user`
 * @property string accessToken
 * @method static EntityFieldSignature F_accessToken() The Signature of: Field `accessToken`
 * @method static ConditionField C_accessToken() The Condition Field of: Field `accessToken`
 * @property string code
 * @method static EntityFieldSignature F_code() The Signature of: Field `code`
 * @method static ConditionField C_code() The Condition Field of: Field `code`
 * @property mixed codeSentAt
 * @method static EntityFieldSignature F_codeSentAt() The Signature of: Field `codeSentAt`
 * @method static ConditionField C_codeSentAt() The Condition Field of: Field `codeSentAt`
 * @property string codeSentVia
 * @method static EntityFieldSignature F_codeSentVia() The Signature of: Field `codeSentVia`
 * @method static ConditionField C_codeSentVia() The Condition Field of: Field `codeSentVia`
 * @property string device
 * @method static EntityFieldSignature F_device() The Signature of: Field `device`
 * @method static ConditionField C_device() The Condition Field of: Field `device`
 * @property string ip
 * @method static EntityFieldSignature F_ip() The Signature of: Field `ip`
 * @method static ConditionField C_ip() The Condition Field of: Field `ip`
 * @property mixed lastOnline
 * @method static EntityFieldSignature F_lastOnline() The Signature of: Field `lastOnline`
 * @method static ConditionField C_lastOnline() The Condition Field of: Field `lastOnline`
 */
class Session extends Entity
{
    protected static function _fieldSignatures(): array
    {
        return array_merge(parent::_fieldSignatures(), [
            'user' => new EntityFieldSignature(
                static::class, 'user',
                new EntityRelation([
                    'relatedEntity' => User::class,
                    'relation' => 'NI',
                    'invName' => 'sessions',
                    'nullable' => false,
                    'invNullable' => false,
                    'definedOn' => 'here',
                ]),
                null
            ),
            'accessToken' => new EntityFieldSignature(
                static::class, 'accessToken',
                new Text(['maxLength' => 255]),
                ''
            ),
            'code' => new EntityFieldSignature(
                static::class, 'activationCode',
                new Text(['minLength' => 6, 'maxLength' => 6]),
                null
            ),
            'codeSentAt' => new EntityFieldSignature(
                static::class, 'codeSentAt',
                new DateTime([]),
                null
            ),
            'codeSentVia' => new EntityFieldSignature(
                static::class, 'activationCode',
                new Enum(['values' => ['sms', 'email']]),
                null
            ),
            'device' => new EntityFieldSignature(
                static::class, 'device',
                new Text(['maxLength' => 255, 'nullable' => true]),
                ''
            ),
            'ip' => new EntityFieldSignature(
                static::class, 'ip',
                new Text(['maxLength' => 15]),
                ''
            ),
            'lastOnline' => new EntityFieldSignature(
                static::class, 'lastOnline',
                new DateTime([]),
                null
            ),
        ]);
    }

    protected static function _indexes(): array
    {
        return array_merge(parent::_indexes(), [
        ]);
    }
}