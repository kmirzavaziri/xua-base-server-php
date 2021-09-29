<?php


namespace XUA\Supers\Files;

use XUA\Services\FileSize;
use XUA\Services\ConstantService;
use XUA\Services\ExpressionService;
use XUA\Services\FileInstance;
use XUA\Services\FileInstanceSame;
use XUA\Supers\Boolean;
use XUA\Supers\Highers\Sequence;
use XUA\Supers\Numerics\Integer;
use XUA\Supers\Strings\Text;
use XUA\Eves\Super;
use XUA\Tools\Signature\SuperArgumentSignature;

/**
 * @property ?array allowedMimeTypes
 * @method static SuperArgumentSignature A_allowedMimeTypes() The Signature of: Argument `allowedMimeTypes`
 * @property ?int maxSize
 * @method static SuperArgumentSignature A_maxSize() The Signature of: Argument `maxSize`
 * @property ?string storageDir
 * @method static SuperArgumentSignature A_storageDir() The Signature of: Argument `storageDir`
 * @property bool nullable
 * @method static SuperArgumentSignature A_nullable() The Signature of: Argument `nullable`
 */
class Generic extends Super
{
    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            'allowedMimeTypes' => new SuperArgumentSignature(new Sequence(['type' => new Text([]), 'nullable' => true]), false, null, false),
            'maxSize' => new SuperArgumentSignature(new Integer(['nullable' => true]), false, null, false),
            'storageDir' => new SuperArgumentSignature(new Text(['nullable' => true]), false, null, false),
            'nullable' => new SuperArgumentSignature(new Boolean([]), false, false, false),
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
            $message = ExpressionService::get('errormessage.invalid.file');
            return false;
        }

        if ($this->allowedMimeTypes !== null) {
            if (!in_array($input->mime, $this->allowedMimeTypes)) {
                $message = ExpressionService::get('errormessage.invalid.file.format');
                return false;
            }
        }

        if ($this->maxSize !== null) {
            if ($input->size > $this->maxSize) {
                $message = ExpressionService::get('errormessage.invalid.file.size', [
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
            return new FileInstance($_FILES[$input]['tmp_name'],  pathinfo($_FILES[$input]['name'], PATHINFO_EXTENSION), false);
        }
        return $input;
    }

    protected function _marshal(mixed $input): mixed
    {
        /** @var FileInstance $input */
        return $input ? (ConstantService::get('config/site', 'url') . '/' . $input->path) : null;
    }

    protected function _marshalDatabase(mixed $input): mixed
    {
        /** @var FileInstance $input */
        $input?->store($this->storageDir ?? ConstantService::STORAGE_PATH);
        return $input?->path;
    }

    protected function _unmarshalDatabase(mixed $input): mixed
    {
        if (!is_string($input)) {
            return $input;
        }
        return file_exists($input) ? new FileInstance($input) : null;
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