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
 * @property ?\Services\XUA\FileInstance Q_birthCertificatePicture
 * @method static MethodItemSignature Q_birthCertificatePicture() The Signature of: Request Item `birthCertificatePicture`
 */
class SetOneBirthCertificatePicture extends SetOneByIdAdmin
{
    protected static function entity(): string
    {
        return User::class;
    }

    protected static function fields(): array
    {
        return [
            new VarqueMethodFieldSignature(User::F_birthCertificatePicture(), false, null, false),
        ];
    }

    protected function body(): void
    {
        /** @var User $user */
        $user = $this->feed();
        if ($user->birthCertificatePicture and file_exists($user->birthCertificatePicture->path)) {
            unlink($user->birthCertificatePicture->path);
        }
        $this->Q_birthCertificatePicture?->store(ConstantService::STORAGE_PATH . DIRECTORY_SEPARATOR . $user->id);
        $user->birthCertificatePicture = $this->Q_birthCertificatePicture;
        $user->store();
    }
}