<?php

namespace Methods\Admin\Farm\Media;

use Entities\Farm;
use Entities\Farm\Media;
use Methods\Abstraction\SetOneByIdAdmin;
use Services\XUA\ConstantService;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

/**
 * @property int Q_id
 * @method static MethodItemSignature Q_id() The Signature of: Request Item `id`
 * @property int Q_farm
 * @method static MethodItemSignature Q_farm() The Signature of: Request Item `farm`
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
            Media::F_farm(),
            Media::F_source(),
        ]);
    }

    protected function body(): void
    {
        /** @var Media $media */
        $media = $this->feed();
        if ($media and $media->source and file_exists($media->source->path)) {
            unlink($media->source->path);
        }
        $this->Q_source?->store(ConstantService::STORAGE_PATH . DIRECTORY_SEPARATOR . Media::table() . DIRECTORY_SEPARATOR . $media->id);
        $media->source = $this->Q_source;
        $media->farm = new Farm($this->Q_farm);
        $media->store();
    }
}