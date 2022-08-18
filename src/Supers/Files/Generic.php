<?php

namespace Xua\Core\Supers\Files;

use Xua\Core\Services\FileSize;
use Xua\Core\Services\ExpressionService;
use Xua\Core\Services\FileInstance;
use Xua\Core\Services\FileInstanceSame;
use Xua\Core\Services\Mime;
use Xua\Core\Supers\Boolean;
use Xua\Core\Supers\Highers\Sequence;
use Xua\Core\Supers\Numerics\Integer;
use Xua\Core\Supers\Strings\Text;
use Xua\Core\Eves\Super;
use Xua\Core\Tools\Signature\Signature;

/**
 * @property ?array allowedMimeTypes
 * @property ?int maxSize
 * @property ?string storageDir
 * @property bool nullable
 */
class Generic extends Super
{
    const allowedMimeTypes = self::class . '::allowedMimeTypes';
    const maxSize = self::class . '::maxSize';
    const storageDir = self::class . '::storageDir';
    const nullable = self::class . '::nullable';

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(false, static::allowedMimeTypes, false, null,
                new Sequence([Sequence::type => new Text([]), Sequence::nullable => true])
            ),
            Signature::new(false, static::maxSize, false, null,
                new Integer([Integer::nullable => true])
            ),
            Signature::new(false, static::storageDir, false, '',
                new Text([])
            ),
            Signature::new(false, static::nullable, false, false,
                new Boolean([])
            ),
        ]);
    }

    protected function _predicate($input, null|string|array &$message = null): bool
    {
        if ($this->nullable and $input === null) {
            return true;
        }

        if (is_a($input, FileInstanceSame::class)) {
            return true;
        }

        if (!is_a($input, FileInstance::class)) {
            $message = ExpressionService::getXua('supers.files.generic.error_message.invalid_file');
            return false;
        }

        if ($this->allowedMimeTypes !== null) {
            if (!in_array($input->mime, $this->allowedMimeTypes)) {
                $message = ExpressionService::getXua('supers.files.generic.error_message.invalid_file_format');
                return false;
            }
        }

        if ($this->maxSize !== null) {
            if ($input->size > $this->maxSize) {
                $message = ExpressionService::getXua('supers.files.generic.error_message.invalid_file_size', [
                    'size' => FileSize::decorate($input->size),
                    'maxSize' => FileSize::decorate($this->maxSize),
                ]);
                return false;
            }
        }

        return true;
    }

    protected function _unmarshal(mixed $input): mixed
    {
        if (!is_string($input)) {
            return $input;
        }
        if ($input == FileInstanceSame::SAME) {
            return new FileInstanceSame();
        }
        if (isset($_FILES[$input]) and !$_FILES[$input]['error']) {
            $extension = Mime::TO_EXTENSION[$_FILES[$input]['type']] ?? pathinfo($_FILES[$input]['name'], PATHINFO_EXTENSION);
            return new FileInstance($_FILES[$input]['tmp_name'], $extension, false);
        }
        return $input;
    }

    protected function _marshal(mixed $input): mixed
    {
        /** @var FileInstance $input */
        return $input?->url;
    }

    protected function _marshalDatabase(mixed $input): mixed
    {
        /** @var FileInstance $input */
        $input?->store($this->storageDir);
        return $input?->url;
    }

    protected function _unmarshalDatabase(mixed $input): mixed
    {
        if (!is_string($input)) {
            return $input;
        }
        return FileInstance::fromUrl($input);
    }

    protected function _databaseType(): ?string
    {
        $nullExpression = $this->nullable ? 'NULL' : ' NOT NULL';
        return "VARCHAR(500)$nullExpression";
    }

    protected function _phpType(): string
    {
        return ($this->nullable ? '?' : '') . '\\' . FileInstance::class;
    }
}