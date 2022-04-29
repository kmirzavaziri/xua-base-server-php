<?php

namespace Xua\Core\Supers\Special;

use Xua\Core\Eves\Entity;
use Xua\Core\Exceptions\DefinitionException;
use Xua\Core\Exceptions\SuperValidationException;
use Xua\Core\Eves\Super;
use Xua\Core\Supers\Highers\Callback;
use Xua\Core\Supers\Highers\Instance;
use Xua\Core\Supers\Highers\Map;
use Xua\Core\Supers\Highers\StructuredMap;
use Xua\Core\Supers\Strings\Enum;
use Xua\Core\Supers\Strings\Symbol;
use Xua\Core\Supers\Strings\Text;
use Xua\Core\Supers\Universal;
use Xua\Core\Tools\Signature\Signature;
use Xua\Core\Tools\SignatureValueCalculator;

/**
 * @property ?array tree
 * @property ?array instant
 * @property ?string name
 * @property null|\Xua\Core\Eves\Super type
 * @property ?string mode
 */
class EntityFieldScheme extends Super
{
    const tree = self::class . '::tree';
    const instant = self::class . '::instant';
    const name = self::class . '::name';
    const type = self::class . '::type';
    const mode = self::class . '::mode';

    const MODE_TREE = 'tree';
    const MODE_INSTANT = 'instant';
    const MODE_ = [
        self::MODE_TREE,
        self::MODE_INSTANT,
    ];

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(false, static::tree, false, null,
                new Map([Map::nullable => true])
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
        if ($this->tree !== null and $this->instant !== null) {
            $exception->setError('instant', 'Specify exactly one of tree and instant.');
        } elseif ($this->tree !== null and $this->instant === null) {
            $roots = array_keys($this->tree);
            if (count($roots) != 1) {
                $exception->setError('tree', 'There must be exactly one root');
                return;
            }
            $rootName = $roots[0];
            try {
                [$root, $this->type] = SignatureValueCalculator::signatureTreeRootAndType($rootName, $this->tree[$rootName]);
                $this->name = $root->name;
            } catch (DefinitionException $e) {
                $exception->setError('tree', $e->getMessage());
            }
            $this->mode = self::MODE_TREE;
        } elseif ($this->tree === null and $this->instant !== null) {
            $this->name = $this->instant['name'];
            $this->type = $this->instant['type'] ?? new Universal([]);
            $this->mode = self::MODE_INSTANT;
        } else {
            $exception->setError('instant', 'Specify exactly one of tree and instant.');
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