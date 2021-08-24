<?php

namespace Methods\Admin\Product\Media;

use Entities\Product;
use Entities\Product\Media;
use Methods\Abstraction\SetOneByIdAdmin;
use Services\XUA\ConstantService;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

/**
 * @property int Q_id
 * @method static MethodItemSignature Q_id() The Signature of: Request Item `id`
 * @property int Q_product
 * @method static MethodItemSignature Q_product() The Signature of: Request Item `product`
 * @property \Services\XUA\FileInstance Q_source
 * @method static MethodItemSignature Q_source() The Signature of: Request Item `source`
 */
class SetOne extends SetOneByIdAdmin
{
    protected static function entity(): string
    {
        return Media::class;
    }

    protected static function fields(): array
    {
        return VarqueMethodFieldSignature::fromList([
            Media::F_product(),
            Media::F_source(),
        ], false, null, false);
    }

    protected function body(): void
    {
        /** @var Media $media */
        $media = $this->feed();
        if ($media and $media->source and file_exists($media->source->path)) {
            unlink($media->source->path);
        }
        $this->Q_source?->store(ConstantService::STORAGE_PATH . DIRECTORY_SEPARATOR . 'medias' . DIRECTORY_SEPARATOR . $media->id);
        $media->source = $this->Q_source;
        $media->product = new Product($this->Q_product);
        $media->store();
    }
}