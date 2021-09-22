<?php

declare(strict_types=1);

namespace Wirgen\LaravelMfa\Providers;

use Illuminate\Database\Eloquent\Model;
use OTPHP\TOTP;
use Wirgen\LaravelMfa\Models\Mfa;
use Wirgen\LaravelMfa\Models\MfaTotp;

class TotpProvider extends AbstractProvider
{
    public $mfaType = 'totp';

    public function register(Model $subject): array
    {
        parent::register($subject);

        $totpModel = MfaTotp::firstOrNew(['mfa_id' => Mfa::getMfaModel($subject)->getKey()]);
        if (!$totpModel->exists) {
            $totpModel->generateSecret();
        }

        return ['secret' => $totpModel->secret];
    }

    public function unregister(Model $subject): void
    {
        parent::unregister($subject);

        MfaTotp::where('mfa_id', Mfa::getMfaModel($subject)->getKey())->delete();
    }

    public function enable(Model $subject): bool
    {
        $result = MfaTotp::where('mfa_id', Mfa::getMfaModel($subject)->getKey())->exists();

        if ($result) {
            parent::enable($subject);
        }

        return $result;
    }

    public function verify(Model $subject, string $payload): bool
    {
        $totpModel = MfaTotp::where('mfa_id', Mfa::getMfaModel($subject)->getKey())->first();

        if (isset($totpModel->secret) && !empty($totpModel->secret)) {
            $totp = TOTP::create(
                $totpModel->secret,
                config('mfa_totp.key.period'),
                config('mfa_totp.key.algorithm'),
                config('mfa_totp.key.length')
            );

            return $totp->verify($payload);
        }

        return false;
    }
}
