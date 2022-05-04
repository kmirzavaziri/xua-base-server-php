<?php

namespace Xua\Core\Supers\Special;

use Xua\Core\Eves\Entity;
use Xua\Core\Eves\MethodEve;
use Xua\Core\Exceptions\DefinitionException;
use Xua\Core\Exceptions\SuperValidationException;
use Xua\Core\Eves\Super;
use Xua\Core\Supers\Highers\Callback;
use Xua\Core\Supers\Highers\Instance;
use Xua\Core\Supers\Highers\Nullable;
use Xua\Core\Supers\Highers\Sequence;
use Xua\Core\Supers\Highers\StructuredMap;
use Xua\Core\Supers\Strings\Enum;
use Xua\Core\Supers\Strings\Symbol;
use Xua\Core\Supers\Strings\Text;
use Xua\Core\Supers\Universal;
use Xua\Core\Tools\Signature\Signature;

/**
 * @property null|\Xua\Core\Tools\Signature\Signature signature
 * @property null|\Xua\Core\Tools\Signature\Signature identifierField
 * @property ?\Xua\Core\Supers\Special\EntityFieldScheme[] children
 * @property ?array instant
 * @property ?string name
 * @property null|\Xua\Core\Eves\Super type
 * @property ?string mode
 */
class EntityFieldScheme extends Super
{
    // given args
    const signature = self::class . '::signature';
    const identifierField = self::class . '::identifierField';
    const children = self::class . '::children';
    const instant = self::class . '::instant';
    // constant args
    const name = self::class . '::name';
    const type = self::class . '::type';
    const mode = self::class . '::mode';

    const MODE_SIGNATURE = 'signature';
    const MODE_INSTANT = 'instant';
    const MODE_ = [
        self::MODE_SIGNATURE,
        self::MODE_INSTANT,
    ];

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(false, static::signature, false, null,
                new Instance([Instance::nullable => true, Instance::of => Signature::class])
            ),
            Signature::new(false, static::identifierField, false, null,
                new Instance([Instance::nullable => true, Instance::of => Signature::class])
            ),
            Signature::new(false, static::children, false, null,
                new Sequence([
                    Sequence::nullable => true,
                    Sequence::type => new Instance([Instance::nullable => false, Instance::of => EntityFieldScheme::class])
                ])
            ),
            Signature::new(false, static::instant, false, null,
                new StructuredMap([
                    StructuredMap::nullable => true,
                    StructuredMap::structure => [
                        'name' => new Symbol([Symbol::nullable => false]),
                        'type' => new Instance([Instance::nullable => true, Instance::of => Super::class]),
                        'getter' => new Callback([
                            Callback::nullable => true,
                            Callback::parameters => [
                                [
                                    'name' => null,
                                    'type' => Entity::class,
                                    'allowSubtype' => true,
                                    'required' => true,
                                    'checkDefault' => false,
                                    'default' => null,
                                    'passByReference' => false,
                                ],
                                [
                                    'name' => null,
                                    'type' => MethodEve::class,
                                    'allowSubtype' => true,
                                    'required' => true,
                                    'checkDefault' => false,
                                    'default' => null,
                                    'passByReference' => false,
                                ],
                            ]
                        ]),
                        'setter' => new Callback([
                            Callback::nullable => true,
                            // @TODO must set return to void
                            Callback::parameters => [
                                [
                                    'name' => null,
                                    'type' => Entity::class,
                                    'allowSubtype' => true,
                                    'required' => true,
                                    'checkDefault' => false,
                                    'default' => null,
                                    'passByReference' => false,
                                ],
                                [
                                    'name' => null,
                                    'type' => null,
                                    'allowSubtype' => true,
                                    'required' => true,
                                    'checkDefault' => false,
                                    'default' => null,
                                    'passByReference' => false,
                                ],
                                [
                                    'name' => null,
                                    'type' => MethodEve::class,
                                    'allowSubtype' => true,
                                    'required' => true,
                                    'checkDefault' => false,
                                    'default' => null,
                                    'passByReference' => false,
                                ],
                            ]
                        ]),
                    ]
                ])
            ),
            Signature::new(true, static::name, false, null,
                new Text([Text::nullable => true])
            ),
            Signature::new(true, static::type, false, null,
                new Instance([Instance::nullable => true, Instance::of => Super::class])
            ),
            Signature::new(true, static::mode, false, null,
                new Enum([Enum::nullable => true, Enum::values => self::MODE_])
            ),
        ]);
    }

    protected function _validation(SuperValidationException $exception): void
    {
        if ($this->signature !== null and $this->instant !== null) {
            $exception->setError('instant', 'Specify exactly one of signature and instant.');
        } elseif ($this->signature !== null) {
            $this->children = $this->children ?? [];
            try {
                if (is_a($this->signature->declaration, EntityRelation::class)) {
                    $this->identifierField = $this->identifierField ?? Signature::_($this->signature->declaration->relatedEntity::id);
                    // @TODO check if $grandChildren (which is just a signature full name as string) is indexed as a unique field
                    if ($this->children) {
                        $structure = [];
                        foreach ($this->children as $child) {
                            if ($this->mode == self::MODE_SIGNATURE and $this->signature->declaration->relatedEntity != $child->signature->class) {
                                throw new DefinitionException("Cannot append a child from entity {$child->signature->declaration->class} to a relational field on entity {$this->signature->declaration->relatedEntity}.");
                            }
                            $structure[$child->name] = $child->type;
                        }
                        $type = new StructuredMap([StructuredMap::structure => $structure, StructuredMap::nullable => $this->signature->declaration->nullable]);
                    } else {
                        $type = new Nullable([Nullable::type => Signature::_($this->signature->declaration->relatedEntity::id)->declaration]);
                    }
                    $type = $this->signature->declaration->toMany ? new Sequence([Sequence::type => $type, Sequence::nullable => $this->signature->declaration->nullable]) : $type;
                    $this->type = $type;
                } else {
                    if ($this->children) {
                        throw new DefinitionException("Cannot append children to a non-relational field {$this->signature->name}.");
                    }
                    $this->type = $this->signature->declaration;
                }
                $this->name = $this->signature->name;
            } catch (DefinitionException $e) {
                $exception->setError('tree', $e->getMessage());
            }
            $this->mode = self::MODE_SIGNATURE;
        } elseif ($this->instant !== null) {
            if ($this->identifierField !== null) {
                $exception->setError('identifierField', 'Cannot specify identifierField for instant schemes.');
            }
            if ($this->children !== null) {
                $exception->setError('children', 'Cannot specify children for instant schemes.');
            }
            $this->name = $this->instant['name'];
            $this->type = $this->instant['type'] ?? new Universal([]);
            $this->mode = self::MODE_INSTANT;
        } else {
            $exception->setError('instant', 'Specify exactly one of signature and instant arguments.');
        }
    }

    protected function _predicate($input, null|string|array &$message = null): bool
    {
        return false;
    }

    protected function _databaseType(): ?string
    {
        return 'DONT STORE';
    }

    protected function _phpType(): string
    {
        return 'null';
    }
}