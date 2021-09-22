<?php

declare(strict_types=1);

namespace Wirgen\LaravelMfa\Providers;

use Illuminate\Database\Eloquent\Model;
use Wirgen\LaravelMfa\Models\Mfa;
use Wirgen\LaravelMfa\Models\MfaOtp;

class OtpProvider extends AbstractProvider
{
    public $mfaType = 'otp';

    public function register(Model $subject): array
    {
        parent::register($subject);

        $otpModel = MfaOtp::firstOrNew(['mfa_id' => Mfa::getMfaModel($subject)->getKey()]);
        if (!$otpModel->exists) {
            $otpModel->generatePasswords();
        }

        return ['passwords' => $otpModel->passwords->toArray()];
    }

    public function unregister(Model $subject): void
    {
        parent::unregister($subject);

        MfaOtp::where('mfa_id', Mfa::getMfaModel($subject)->getKey())->delete();
    }

    public function enable(Model $subject): bool
    {
        $result = MfaOtp::where('mfa_id', Mfa::getMfaModel($subject)->getKey())->exists();

        if ($result) {
            parent::enable($subject);
        }

        return $result;
    }

    public function regenerate(Model $subject): array
    {
        $otpModel = MfaOtp::firstOrNew(['mfa_id' => Mfa::getMfaModel($subject)->getKey()]);
        $otpModel->generatePasswords();

        return ['passwords' => $otpModel->passwords->toArray()];
    }

    public function verify(Model $subject, string $payload): bool
    {
        $otpModel = MfaOtp::where('mfa_id', Mfa::getMfaModel($subject)->getKey())->first();

        if ($otpModel && $otpModel->passwords->contains($payload)) {
            $passwords = $otpModel->passwords->values();
            $otpModel->passwords = $passwords->diff($payload)->values();
            $otpModel->save();

            return true;
        }

        return false;
    }
}
