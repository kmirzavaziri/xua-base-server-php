<?php

namespace Methods\User;

use Entities\User;
use Services\UserService;
use Services\XUA\ConstantService;
use XUA\Entity;
use XUA\Tools\Signature\MethodItemSignature;
use XUA\Tools\Signature\VarqueMethodFieldSignature;
use XUA\VARQUE\MethodAdjust;

/**
 * @property ?\Services\XUA\FileInstance Q_nationalCardPicture
 * @method static MethodItemSignature Q_nationalCardPicture() The Signature of: Request Item `nationalCardPicture`
 */
class SetNationalCardPicture extends MethodAdjust
{
    protected static function entity(): string
    {
        return User::class;
    }

    protected static function fields(): array
    {
        return [
            new VarqueMethodFieldSignature(User::F_nationalCardPicture(), false, null, false),
        ];
    }

    protected function feed(): Entity
    {
        return UserService::verifyUser($this->error);
    }

    protected function body(): void
    {
        /** @var User $user */
        $user = $this->feed();
        if ($user->nationalCardPicture and file_exists($user->nationalCardPicture->path)) {
            unlink($user->nationalCardPicture->path);
        }
        $this->Q_nationalCardPicture?->store(ConstantService::STORAGE_PATH . DIRECTORY_SEPARATOR . $user->id);
        $user->nationalCardPicture = $this->Q_nationalCardPicture;
        $user->store();
    }
}