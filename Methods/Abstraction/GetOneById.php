<?php

namespace Methods\Abstraction;

use Entities\User;
use Services\UserService;
use Services\XUA\ExpressionService;
use XUA\Entity;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\VARQUE\MethodView;

abstract class GetOneById extends MethodView
{
    protected static function entity(): string
    {
        return '';
    }

    protected static function _requestSignatures(): array
    {
        return array_merge(parent::_requestSignatures(), [
            'id' => new MethodItemSignature(User::F_id()->type, true, null, false),
        ]);
    }

    protected function feed(): Entity
    {
        $entity = (new (static::entity())($this->Q_id));
        if (!$entity->id) {
            $this->addAndThrowError('id', ExpressionService::get('errormessage.invalid.id'));
        }
        return $entity;
    }

    protected function validations(): void
    {
        UserService::verifyUser($this->error);
    }
}