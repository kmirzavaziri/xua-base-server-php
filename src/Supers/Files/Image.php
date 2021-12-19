<?php

namespace Xua\Core\Supers\Files;

use Xua\Core\Services\Mime;
use Xua\Core\Services\ExpressionService;
use Xua\Core\Services\FileInstance;
use Xua\Core\Services\FileInstanceSame;
use Xua\Core\Supers\Highers\Sequence;
use Xua\Core\Supers\Numerics\Integer;
use Xua\Core\Supers\Strings\Enum;
use Xua\Core\Tools\Signature\Signature;

/**
 * @property ?array allowedMimeTypes
 * @property ?int maxSize
 * @property ?string storageDir
 * @property bool nullable
 * @property ?int minWidth
 * @property ?int maxWidth
 * @property ?int minHeight
 * @property ?int maxHeight
 * @property ?int ratioWidth
 * @property ?int ratioHeight
 * @property ?string unifier
 */
class Image extends Generic
{
    const allowedMimeTypes = self::class . '::allowedMimeTypes';
    const maxSize = self::class . '::maxSize';
    const storageDir = self::class . '::storageDir';
    const nullable = self::class . '::nullable';
    const minWidth = self::class . '::minWidth';
    const maxWidth = self::class . '::maxWidth';
    const minHeight = self::class . '::minHeight';
    const maxHeight = self::class . '::maxHeight';
    const ratioWidth = self::class . '::ratioWidth';
    const ratioHeight = self::class . '::ratioHeight';
    const unifier = self::class . '::unifier';

    protected static function _argumentSignatures(): array
    {
        return array_merge(parent::_argumentSignatures(), [
            Signature::new(false, static::minWidth, false, null,
                new Integer([Integer::nullable => true, Integer::unsigned => true])
            ),
            Signature::new(false, static::maxWidth, false, null,
                new Integer([Integer::nullable => true, Integer::unsigned => true])
            ),
            Signature::new(false, static::minHeight, false, null,
                new Integer([Integer::nullable => true, Integer::unsigned => true])
            ),
            Signature::new(false, static::maxHeight, false, null,
                new Integer([Integer::nullable => true, Integer::unsigned => true])
            ),
            Signature::new(false, static::ratioWidth, false, null,
                new Integer([Integer::nullable => true, Integer::unsigned => true])
            ),
            Signature::new(false, static::ratioHeight, false, null,
                new Integer([Integer::nullable => true, Integer::unsigned => true])
            ),
            Signature::new(false, static::allowedMimeTypes, false, Mime::MIME_IMAGE,
                new Sequence([Sequence::type => new Enum([Enum::values => Mime::MIME_IMAGE]), Sequence::nullable => true])
            ),
            Signature::new(false, static::unifier, false, null,
                new Enum([Enum::values => Mime::MIME_IMAGE, Enum::nullable => true])
            ),
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

        if (is_a($input, FileInstanceSame::class)) {
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
            $message = ExpressionService::getXua('supers.files.image.error_message.image.min.width.violated', ['minWidth' => $this->minWidth, 'width' => $width]);
            return false;
        }

        if ($this->maxWidth !== null and $width > $this->maxWidth) {
            $message = ExpressionService::getXua('supers.files.image.error_message.image.max.width.violated', ['maxWidth' => $this->maxWidth, 'width' => $width]);
            return false;
        }

        if ($this->minHeight !== null and $height < $this->minHeight) {
            $message = ExpressionService::getXua('supers.files.image.error_message.image.min.height.violated', ['minHeight' => $this->minHeight, 'height' => $height]);
            return false;
        }

        if ($this->maxHeight !== null and $height > $this->maxHeight) {
            $message = ExpressionService::getXua('supers.files.image.error_message.image.max.height.violated', ['maxHeight' => $this->maxHeight, 'height' => $height]);
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
            $message = ExpressionService::getXua('supers.files.image.error_message.image.ratio.violated', [
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