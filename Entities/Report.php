<?php

namespace Entities;


use Services\Mime;
use Services\Size;
use Services\XUA\ExpressionService;
use Services\XUA\LocaleLanguage;
use Supers\Basics\EntitySupers\EntityRelation;
use Supers\Basics\Files\Generic;
use Supers\Basics\Highers\Date;
use Supers\Basics\Strings\Enum;
use Supers\Basics\Strings\Text;
use Supers\Customs\Name;
use Supers\Customs\Url;
use XUA\Entity;
use XUA\Exceptions\EntityFieldException;
use XUA\Tools\Entity\ConditionField;
use XUA\Tools\Signature\EntityFieldSignature;

/**
 * @property int id
 * @method static EntityFieldSignature F_id() The Signature of: Field `id`
 * @method static ConditionField C_id() The Condition Field of: Field `id`
 * @property \Entities\Item item
 * @method static EntityFieldSignature F_item() The Signature of: Field `item`
 * @method static ConditionField C_item() The Condition Field of: Field `item`
 * @property \Entities\User reporter
 * @method static EntityFieldSignature F_reporter() The Signature of: Field `reporter`
 * @method static ConditionField C_reporter() The Condition Field of: Field `reporter`
 * @property \Services\XUA\DateTimeInstance issueDate
 * @method static EntityFieldSignature F_issueDate() The Signature of: Field `issueDate`
 * @method static ConditionField C_issueDate() The Condition Field of: Field `issueDate`
 * @property string title
 * @method static EntityFieldSignature F_title() The Signature of: Field `title`
 * @method static ConditionField C_title() The Condition Field of: Field `title`
 * @property string type
 * @method static EntityFieldSignature F_type() The Signature of: Field `type`
 * @method static ConditionField C_type() The Condition Field of: Field `type`
 * @property ?string link
 * @method static EntityFieldSignature F_link() The Signature of: Field `link`
 * @method static ConditionField C_link() The Condition Field of: Field `link`
 * @property ?\Services\XUA\FileInstance file
 * @method static EntityFieldSignature F_file() The Signature of: Field `file`
 * @method static ConditionField C_file() The Condition Field of: Field `file`
 * @property string description
 * @method static EntityFieldSignature F_description() The Signature of: Field `description`
 * @method static ConditionField C_description() The Condition Field of: Field `description`
 */
class Report extends Entity
{
    const TYPE_LINK = 'link';
    const TYPE_FILE = 'file';
    const TYPE_ = [
        self::TYPE_LINK,
        self::TYPE_FILE,
    ];

    protected static function _fieldSignatures(): array
    {
        return array_merge(parent::_fieldSignatures(), [
            'item' => new EntityFieldSignature(
                static::class, 'item',
                new EntityRelation([
                    'relatedEntity' => Item::class,
                    'relation' => EntityRelation::REL_RN1,
                    'invName' => 'reports',
                    'definedOn' => EntityRelation::DEFINED_ON_HERE,
                ]),
                null
            ),
            'reporter' => new EntityFieldSignature(
                static::class, 'reporter',
                new EntityRelation([
                    'relatedEntity' => User::class,
                    'relation' => EntityRelation::REL_RN1,
                    'invName' => null,
                    'definedOn' => EntityRelation::DEFINED_ON_HERE,
                ]),
                null
            ),
            'issueDate' => new EntityFieldSignature(
                static::class, 'issueDate',
                new Date(['nullable' => false]),
                null
            ),
            'title' => new EntityFieldSignature(
                static::class, 'title',
                new Name(['nullable' => false, 'minLength' => 1, 'maxLength' => 200, 'language' => LocaleLanguage::LANG_FA]),
                null
            ),
            'type' => new EntityFieldSignature(
                static::class, 'type',
                new Enum(['nullable' => false, 'values' => self::TYPE_]),
                null
            ),
            'link' => new EntityFieldSignature(
                static::class, 'link',
                new Url(['nullable' => true, 'schemes' => ['http://', 'https://']]),
                null
            ),
            'file' => new EntityFieldSignature(
                static::class, 'file',
                new Generic([
                    'nullable' => true,
                    'allowedMimeTypes' => [
                        Mime::MIME_APPLICATION_PDF,
                        Mime::MIME_APPLICATION_MSWORD,
                        Mime::MIME_APPLICATION_MSWORD_X,
                        Mime::MIME_APPLICATION_VND_MS_EXCEL,
                        Mime::MIME_APPLICATION_VND_MS_EXCEL_X,
                    ],
                    'maxSize' => 10 * Size::MB
                ]),
                null
            ),
            'description' => new EntityFieldSignature(
                static::class, 'description',
                new Text(['nullable' => false, 'minLength' => 1, 'maxLength' => 1000]),
                null
            ),
        ]);
    }

    protected static function _indexes(): array
    {
        return array_merge(parent::_indexes(), [
        ]);
    }

    protected function _validation(EntityFieldException $exception): void
    {
        if ($this->type == self::TYPE_LINK) {
            if (!$this->link) {
                $exception->setError('link', ExpressionService::get('errormessage.required.entity.field.not.provided'));
            }
            if ($this->file) {
                $exception->setError('file', ExpressionService::get('errormessage.entity.field.must.be.empty'));
            }
        }
        if ($this->type == self::TYPE_FILE) {
            if (!$this->file) {
                $exception->setError('file', ExpressionService::get('errormessage.required.entity.field.not.provided'));
            }
            if ($this->link) {
                $exception->setError('link', ExpressionService::get('errormessage.entity.field.must.be.empty'));
            }
        }
    }
}