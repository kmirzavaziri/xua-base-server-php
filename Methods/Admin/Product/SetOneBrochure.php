<?php

namespace Methods\Admin\Product;

use Entities\Product;
use Methods\Abstraction\SetOneByIdAdmin;
use Services\XUA\ConstantService;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

/**
 * @property int Q_id
 * @method static MethodItemSignature Q_id() The Signature of: Request Item `id`
 * @property ?\Services\XUA\FileInstance Q_brochure
 * @method static MethodItemSignature Q_brochure() The Signature of: Request Item `brochure`
 */
class SetOneBrochure extends SetOneByIdAdmin
{
    protected static function entity(): string
    {
        return Product::class;
    }

    protected static function fields(): array
    {
        return [
            VarqueMethodFieldSignature::fromSignature(Product::F_brochure(), false, null, false),
        ];
    }

    protected function body(): void
    {
        /** @var Product $product */
        $product = $this->feed();
        if ($product->brochure and file_exists($product->brochure->path)) {
            unlink($product->brochure->path);
        }
        $this->Q_brochure?->store(ConstantService::STORAGE_PATH . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR . $product->id);
        $product->brochure = $this->Q_brochure;
        $product->store();
    }
}