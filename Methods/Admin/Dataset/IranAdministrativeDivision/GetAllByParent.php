<?php

namespace Methods\Admin\Dataset\IranAdministrativeDivision;

use Entities\Dataset\IranAdministrativeDivision;
use Services\UserService;
use Supers\Basics\Numerics\Decimal;
use XUA\Tools\Entity\Condition;
use XUA\Tools\Signature\EntityFieldSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;
use XUA\VARQUE\MethodQuery;
use XUA\Tools\Signature\MethodItemSignature;

/**
 * @property null|int Q_parent
 * @method static MethodItemSignature Q_parent() The Signature of: Request Item `parent`
 * @property array result
 * @method static MethodItemSignature R_result() The Signature of: Response Item `result`
 */
class GetAllByParent extends MethodQuery
{
    protected static function entity(): string
    {
        return IranAdministrativeDivision::class;
    }

    protected static function _requestSignatures(): array
    {
        return array_merge(parent::_requestSignatures(), [
            'parent' => new MethodItemSignature(new Decimal(['nullable' => true, 'integerLength' => 6, 'fractionalLength' => 0, 'base' => 10]), false, null, false),
        ]);
    }

    protected static function fields(): array
    {
        return VarqueMethodFieldSignature::fromList([
            IranAdministrativeDivision::F_id(),
            IranAdministrativeDivision::F_title(),
        ]);
    }

    protected function feed(): array
    {
        if ($this->Q_parent === null) {
            return IranAdministrativeDivision::getMany(Condition::leaf(IranAdministrativeDivision::C_parent()->rel(IranAdministrativeDivision::C_id()), Condition::ISNULL));
        }
        return IranAdministrativeDivision::getMany(Condition::leaf(IranAdministrativeDivision::C_parent()->rel(IranAdministrativeDivision::C_id()), Condition::EQ, $this->Q_parent));
    }

    protected static function wrapper(): string
    {
        return 'result';
    }

    protected static function association(): ?EntityFieldSignature
    {
        return null;
    }

    protected function validations(): void
    {
        UserService::verifyAdmin($this->error);
    }
}