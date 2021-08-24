<?php

namespace Methods\Admin\User;

use Entities\User;
use Methods\Abstraction\SetOneByIdAdmin;
use Services\XUA\ConstantService;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;

/**
 * @property int Q_id
 * @method static MethodItemSignature Q_id() The Signature of: Request Item `id`
 * @property ?\Services\XUA\FileInstance Q_nationalCardPicture
 * @method static MethodItemSignature Q_nationalCardPicture() The Signature of: Request Item `nationalCardPicture`
 */
class SetOneNationalCardPicture extends SetOneByIdAdmin
{
    protected static function entity(): string
    {
        return User::class;
    }

    protected static function fields(): array
    {
        return [
            VarqueMethodFieldSignature::fromSignature(User::F_nationalCardPicture(), false, null, false),
        ];
    }

    protected function body(): void
    {
        /** @var User $user */
        $user = $this->feed();
        if ($user->nationalCardPicture and file_exists($user->nationalCardPicture->path)) {
            unlink($user->nationalCardPicture->path);
        }
        $this->Q_nationalCardPicture?->store(ConstantService::STORAGE_PATH . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . $user->id);
        $user->nationalCardPicture = $this->Q_nationalCardPicture;
        $user->store();
    }
}