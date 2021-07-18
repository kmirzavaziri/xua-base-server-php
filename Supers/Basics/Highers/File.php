<?php


namespace Supers\Basics\Highers;



use Services\Size;
use Services\XUA\ConstantService;
use Services\XUA\ExpressionService;
use Services\XUA\FileInstance;
use Supers\Basics\Boolean;
use Supers\Basics\Numerics\Integer;
use Supers\Basics\Strings\Text;
use XUA\Super;
use XUA\Tools\Signature\SuperArgumentSignature;

/**
 * @property array allowedMimeTypes
 * @method static SuperArgumentSignature A_allowedMimeTypes() The Signature of: Argument `allowedMimeTypes`
 * @property string storageDir
 * @method static SuperArgumentSignature A_storageDir() The Signature of: Argument `storageDir`
 * @property array unifiers
 * @method static SuperArgumentSignature A_unifiers() The Signature of: Argument `unifiers`
 * @property ?string maxSize
 * @method static SuperArgumentSignature A_maxSize() The Signature of: Argument `maxSize`
 * @property bool nullable
 * @method static SuperArgumentSignature A_nullable() The Signature of: Argument `nullable`
 */
class File extends Super
{
    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            'allowedMimeTypes' => new SuperArgumentSignature(new Sequence(['type' => new Text([]), 'nullable' => true]), false, null, false),
            'maxSize' => new SuperArgumentSignature(new Integer(['nullable' => true]), false, null, false),
            'storageDir' => new SuperArgumentSignature(new Text(['nullable' => true]), false, null, false),
            'unifiers' => new SuperArgumentSignature(new Sequence(['type' => new Text([])]), false, null, false),
            'nullable' => new SuperArgumentSignature(new Boolean([]), false, false, false),
        ]);
    }

    protected function _predicate($input, string &$message = null): bool
    {
        if ($this->nullable and $input === null) {
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
                    'size' => Size::decorate($input->size),
                    'maxSize' => Size::decorate($this->maxSize),
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
        if (isset($_FILES[$input]) and !$_FILES[$input]['error']) {
            return new FileInstance($_FILES[$input]['tmp_name'],  pathinfo($_FILES[$input]['name'], PATHINFO_EXTENSION), $this->unifiers, false, false);
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
        $input->store($this->storageDir ?? ConstantService::STORAGE_PATH);
        return $input->path;
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