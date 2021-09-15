<?php


namespace Supers\Basics\Files;


use Services\Mime;
use Services\XUA\ExpressionService;
use Services\XUA\FileInstance;
use Services\XUA\FileInstanceSame;
use Supers\Basics\Highers\Sequence;
use Supers\Basics\Numerics\Integer;
use Supers\Basics\Strings\Enum;
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
 * @property ?int minWidth
 * @method static SuperArgumentSignature A_minWidth() The Signature of: Argument `minWidth`
 * @property ?int maxWidth
 * @method static SuperArgumentSignature A_maxWidth() The Signature of: Argument `maxWidth`
 * @property ?int minHeight
 * @method static SuperArgumentSignature A_minHeight() The Signature of: Argument `minHeight`
 * @property ?int maxHeight
 * @method static SuperArgumentSignature A_maxHeight() The Signature of: Argument `maxHeight`
 * @property ?int ratioWidth
 * @method static SuperArgumentSignature A_ratioWidth() The Signature of: Argument `ratioWidth`
 * @property ?int ratioHeight
 * @method static SuperArgumentSignature A_ratioHeight() The Signature of: Argument `ratioHeight`
 * @property ?string unifier
 * @method static SuperArgumentSignature A_unifier() The Signature of: Argument `unifier`
 */
class Image extends Generic
{
    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            'minWidth' => new SuperArgumentSignature(new Integer(['nullable' => true, 'unsigned' => true]), false, null, false),
            'maxWidth' => new SuperArgumentSignature(new Integer(['nullable' => true, 'unsigned' => true]), false, null, false),
            'minHeight' => new SuperArgumentSignature(new Integer(['nullable' => true, 'unsigned' => true]), false, null, false),
            'maxHeight' => new SuperArgumentSignature(new Integer(['nullable' => true, 'unsigned' => true]), false, null, false),
            'ratioWidth' => new SuperArgumentSignature(new Integer(['nullable' => true, 'unsigned' => true]), false, null, false),
            'ratioHeight' => new SuperArgumentSignature(new Integer(['nullable' => true, 'unsigned' => true]), false, null, false),
            'allowedMimeTypes' => new SuperArgumentSignature(new Sequence(['type' => new Enum(['values' => Mime::MIME_IMAGE]), 'nullable' => true]), false, Mime::MIME_IMAGE, false),
            'unifier' => new SuperArgumentSignature(new Enum(['values' => Mime::MIME_IMAGE, 'nullable' => true]), false, null, false),
        ]);
    }

    protected function _predicate($input, null|string|array &$message = null): bool
    {
        if (!parent::_predicate($input, $message)) {
            return false;
        }

        if ($this->nullable and $input === null) {
            return true;
        }

        if (!is_a($input, FileInstanceSame::class)) {
            return true;
        }

        /** @var FileInstance $input */
        $image = match ($input->mime) {
            Mime::MIME_IMAGE_PNG => imagecreatefrompng($input->path),
            Mime::MIME_IMAGE_JPEG => imagecreatefromjpeg($input->path),
            Mime::MIME_IMAGE_GIF => imagecreatefromgif($input->path),
            Mime::MIME_IMAGE_BMP => imagecreatefrombmp($input->path),
        };
        if (!$image) {
            return false;
        }
        $width = imagesx($image);
        $height = imagesy($image);

        if ($this->minWidth !== null and $width < $this->minWidth) {
            $message = ExpressionService::get('errormessage.image.min.width.violated', ['minWidth' => $this->minWidth, 'width' => $width]);
            return false;
        }

        if ($this->maxWidth !== null and $width > $this->maxWidth) {
            $message = ExpressionService::get('errormessage.image.max.width.violated', ['maxWidth' => $this->maxWidth, 'width' => $width]);
            return false;
        }

        if ($this->minHeight !== null and $height < $this->minHeight) {
            $message = ExpressionService::get('errormessage.image.min.height.violated', ['minHeight' => $this->minHeight, 'height' => $height]);
            return false;
        }

        if ($this->maxHeight !== null and $height > $this->maxHeight) {
            $message = ExpressionService::get('errormessage.image.max.height.violated', ['maxHeight' => $this->maxHeight, 'height' => $height]);
            return false;
        }

        if ($this->ratioWidth !== null and $this->ratioHeight !== null and $this->ratioWidth / $this->ratioHeight != $width / $height) {
            if($width < $height) {
                $lesser = $width;
                $greater = $height;
            } else {
                $lesser = $height;
                $greater = $width;
            }
            $remainder = $greater % $lesser;
            while($remainder > 0) {
                $greater = $lesser;
                $lesser = $remainder;
                $remainder = $greater % $lesser;
            }
            $gcd = $lesser;
            $numerator = $width / $gcd;
            $denominator = $height / $gcd;
            $message = ExpressionService::get('errormessage.image.ratio.violated', [
                'formalRatio' => "$this->ratioWidth:$this->ratioHeight",
                'actualRatio' => "$numerator:$denominator"
            ]);
            return false;
        }

        return true;
    }

    protected function _unmarshal(mixed $input): mixed
    {
        $input = parent::_unmarshal($input);
        if(!is_a($input, FileInstance::class)) {
            return $input;
        }
        if ($this->unifier) {
            $image = match ($input->mime) {
                Mime::MIME_IMAGE_PNG => imagecreatefrompng($input->path),
                Mime::MIME_IMAGE_JPEG => imagecreatefromjpeg($input->path),
                Mime::MIME_IMAGE_GIF => imagecreatefromgif($input->path),
                Mime::MIME_IMAGE_BMP => imagecreatefrombmp($input->path),
            };
            if (!$image) {
                return $input;
            }
            switch ($this->unifier) {
                case Mime::MIME_IMAGE_PNG:
                    imagepng($image, $input->path);
                    $input->extension = 'png';
                    break;
                case Mime::MIME_IMAGE_JPEG:
                    imagejpeg($image, $input->path);
                    $input->extension = 'jpg';
                    break;
                case Mime::MIME_IMAGE_GIF:
                    imagegif($image, $input->path);
                    $input->extension = 'jpg';
                    break;
                case Mime::MIME_IMAGE_BMP:
                    imagebmp($image, $input->path);
                    $input->extension = 'jpg';
                    break;
            }
            imagedestroy($image);
        }
        return $input;
    }
}