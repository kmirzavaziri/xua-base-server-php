<?php

namespace Methods\Admin\Item;

use Entities\Item;
use Methods\Abstraction\SetOneByIdAdmin;
use Services\XUA\ExpressionService;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

/**
 * @property int Q_id
 * @method static MethodItemSignature Q_id() The Signature of: Request Item `id`
 * @property int Q_product
 * @method static MethodItemSignature Q_product() The Signature of: Request Item `product`
 * @property string Q_status
 * @method static MethodItemSignature Q_status() The Signature of: Request Item `status`
 */
class SetOne extends SetOneByIdAdmin
{
    const FORBIDDEN_STATUS_TRANSITIONS = [
        [Item::STATUS_UNTRACKED, Item::STATUS_RESERVED],
        [Item::STATUS_UNTRACKED, Item::STATUS_WITHHELD],
        [Item::STATUS_UNTRACKED, Item::STATUS_SOLD],

        [Item::STATUS_UNAVAILABLE, Item::STATUS_UNTRACKED],
        [Item::STATUS_UNAVAILABLE, Item::STATUS_RESERVED],
        [Item::STATUS_UNAVAILABLE, Item::STATUS_WITHHELD],
        [Item::STATUS_UNAVAILABLE, Item::STATUS_SOLD],

        [Item::STATUS_AVAILABLE, Item::STATUS_UNTRACKED],
        [Item::STATUS_AVAILABLE, Item::STATUS_WITHHELD],
        [Item::STATUS_AVAILABLE, Item::STATUS_SOLD],

        [Item::STATUS_RESERVED, Item::STATUS_UNTRACKED],
        [Item::STATUS_RESERVED, Item::STATUS_UNAVAILABLE],

        [Item::STATUS_WITHHELD, Item::STATUS_UNTRACKED],
        [Item::STATUS_WITHHELD, Item::STATUS_UNAVAILABLE],
        [Item::STATUS_WITHHELD, Item::STATUS_AVAILABLE],
        [Item::STATUS_WITHHELD, Item::STATUS_RESERVED],

        [Item::STATUS_SOLD, Item::STATUS_UNTRACKED],
        [Item::STATUS_SOLD, Item::STATUS_UNAVAILABLE],
        [Item::STATUS_SOLD, Item::STATUS_AVAILABLE],
        [Item::STATUS_SOLD, Item::STATUS_RESERVED],
        [Item::STATUS_SOLD, Item::STATUS_WITHHELD],

        [Item::STATUS_LOST, Item::STATUS_UNTRACKED],
        [Item::STATUS_LOST, Item::STATUS_UNAVAILABLE],
        [Item::STATUS_LOST, Item::STATUS_AVAILABLE],
        [Item::STATUS_LOST, Item::STATUS_RESERVED],
        [Item::STATUS_LOST, Item::STATUS_WITHHELD],
        [Item::STATUS_LOST, Item::STATUS_SOLD],
    ];
    protected static function entity(): string
    {
        return Item::class;
    }

    protected static function fields(): array
    {
        return VarqueMethodFieldSignature::fromList([
            Item::F_product(),
            Item::F_status(),
        ]);
    }

    protected function validations(): void
    {
        parent::validations();
        $feed = $this->feed();
        if (!$feed->id) {
            $this->addAndThrowError('product', ExpressionService::get('errormessage.cant.create.new.item.by.this.method'));
        }
        if (in_array([$feed->status, $this->Q_status], self::FORBIDDEN_STATUS_TRANSITIONS)) {
            $this->addAndThrowError('status', ExpressionService::get('errormessage.cant.change.item.status.from.status.to.status', [
                'oldStatus' => $feed->status,
                'newStatus' => $this->Q_status,
            ]));
        }
        if (!in_array($this->Q_status, [Item::STATUS_UNTRACKED, Item::STATUS_UNAVAILABLE]) and $feed->product->id != $this->Q_product) {
            $this->addAndThrowError('product', ExpressionService::get('errormessage.cant.change.item.product.for.status.status', [
                'status' => $this->Q_status,
            ]));
        }
    }

}