<?php

namespace Methods\Admin\Item;

use Entities\Item;
use Entities\Product;
use Services\ItemService;
use Services\UserService;
use Services\XUA\ExpressionService;
use Supers\Basics\Numerics\DecimalRange;
use XUA\Method;
use XUA\Tools\Signature\MethodItemSignature;

/**
 * @property int Q_product
 * @method static MethodItemSignature Q_product() The Signature of: Request Item `product`
 * @property int Q_count
 * @method static MethodItemSignature Q_count() The Signature of: Request Item `count`
 */
class GenerateMany extends Method
{
    protected static function _requestSignatures(): array
    {
        return array_merge(parent::_requestSignatures(), [
            'product' => new MethodItemSignature(Product::F_id()->type, true, null, false),
            'count' => new MethodItemSignature(new DecimalRange(['min' => 1, 'max' => 1000, 'fractionalLength' => 0]), true, null, false),
        ]);
    }

    protected function validations(): void
    {
        UserService::verifyAdmin($this->error);
    }

    protected function body(): void
    {
        $product = new Product($this->Q_product);
        if (!$product->id) {
            $this->addAndThrowError('product', ExpressionService::get('errormessage.entity.with.id.does.not.exists', [
                'id' => $this->Q_product,
                'entity' => ExpressionService::get('entityclass.Product')
            ]));
        }

        $items = [];
        for ($i = 0; $i < $this->Q_count; $i++) {
            $item = new Item();
            $item->product = $product;
            $item->status = Item::STATUS_UNTRACKED;
            $item->store();
            $items[] = $item;
        }

        foreach ($items as $item) {
            $item->code = ItemService::generateCode($item);
            $item->store();
        }
    }
}