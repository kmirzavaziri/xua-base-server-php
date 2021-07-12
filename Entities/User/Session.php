<?php

namespace Entities\User;


use Entities\User;
use Services\IPLocationService;
use Services\XUA\ConstantService;
use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\EntitySupers\PhpVirtualField;
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
 * @property string verificationCode
 * @method static EntityFieldSignature F_verificationCode() The Signature of: Field `verificationCode`
 * @method static ConditionField C_verificationCode() The Condition Field of: Field `verificationCode`
 * @property \Services\XUA\DateTimeInstance codeSentAt
 * @method static EntityFieldSignature F_codeSentAt() The Signature of: Field `codeSentAt`
 * @method static ConditionField C_codeSentAt() The Condition Field of: Field `codeSentAt`
 * @property string codeSentVia
 * @method static EntityFieldSignature F_codeSentVia() The Signature of: Field `codeSentVia`
 * @method static ConditionField C_codeSentVia() The Condition Field of: Field `codeSentVia`
 * @property ?string systemInfo
 * @method static EntityFieldSignature F_systemInfo() The Signature of: Field `systemInfo`
 * @method static ConditionField C_systemInfo() The Condition Field of: Field `systemInfo`
 * @property string ip
 * @method static EntityFieldSignature F_ip() The Signature of: Field `ip`
 * @method static ConditionField C_ip() The Condition Field of: Field `ip`
 * @property string location
 * @method static EntityFieldSignature F_location() The Signature of: Field `location`
 * @method static ConditionField C_location() The Condition Field of: Field `location`
 * @property null|\Services\XUA\DateTimeInstance lastOnline
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
            'verificationCode' => new EntityFieldSignature(
                static::class, 'verificationCode',
                new Text(['minLength' => ConstantService::VERIFICATION_CODE_LENGTH, 'maxLength' => ConstantService::VERIFICATION_CODE_LENGTH]),
                null
            ),
            'codeSentAt' => new EntityFieldSignature(
                static::class, 'codeSentAt',
                new DateTime([]),
                null
            ),
            'codeSentVia' => new EntityFieldSignature(
                static::class, 'codeSentVia',
                new Enum(['values' => ['sms', 'email']]),
                null
            ),
            'systemInfo' => new EntityFieldSignature(
                static::class, 'systemInfo',
                new Text(['maxLength' => 255, 'nullable' => true]),
                ''
            ),
            'ip' => new EntityFieldSignature(
                static::class, 'ip',
                new Text(['maxLength' => 15]),
                ''
            ),
            'location' => new EntityFieldSignature(
                static::class, 'location',
                new PhpVirtualField(['getter' => [IPLocationService::class, 'locationFromIp']]),
                null
            ),
            'lastOnline' => new EntityFieldSignature(
                static::class, 'lastOnline',
                new DateTime(['nullable' => true]),
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