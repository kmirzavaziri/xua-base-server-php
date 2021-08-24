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
 * @property ?\Services\XUA\FileInstance Q_profilePicture
 * @method static MethodItemSignature Q_profilePicture() The Signature of: Request Item `profilePicture`
 */
class SetProfilePicture extends MethodAdjust
{
    protected static function entity(): string
    {
        return User::class;
    }

    protected static function fields(): array
    {
        return [
            VarqueMethodFieldSignature::fromSignature(User::F_profilePicture(), false, null, false),
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
        if ($user->profilePicture and file_exists($user->profilePicture->path)) {
            unlink($user->profilePicture->path);
        }
        $this->Q_profilePicture?->store(ConstantService::STORAGE_PATH . DIRECTORY_SEPARATOR . $user->id);
        $user->profilePicture = $this->Q_profilePicture;
        $user->store();
    }
}