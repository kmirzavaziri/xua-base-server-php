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
 * @property ?\Services\XUA\FileInstance Q_idBookletPicture
 * @method static MethodItemSignature Q_idBookletPicture() The Signature of: Request Item `idBookletPicture`
 */
class SetIdBookletPicture extends MethodAdjust
{
    protected static function entity(): string
    {
        return User::class;
    }

    protected static function fields(): array
    {
        return [
            VarqueMethodFieldSignature::fromSignature(User::F_idBookletPicture(), false, null, false),
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
        if ($user->idBookletPicture and file_exists($user->idBookletPicture->path)) {
            unlink($user->idBookletPicture->path);
        }
        $this->Q_idBookletPicture?->store(ConstantService::STORAGE_PATH . DIRECTORY_SEPARATOR . User::table() . DIRECTORY_SEPARATOR . $user->id);
        $user->idBookletPicture = $this->Q_idBookletPicture;
        $user->store();
    }
}