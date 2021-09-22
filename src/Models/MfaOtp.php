<?php

declare(strict_types=1);

namespace Wirgen\LaravelMfa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Class MfaOtp
 * @package Wirgen\LaravelMfa\Models
 *
 * @property int $id
 * @property int $mfa_id
 * @property Collection $passwords
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class MfaOtp extends Model
{
    public const ALPHABET = 'abcdefghijklmnopqrstuvwxyz0123456789';

    protected $fillable = ['mfa_id'];

    protected $casts = [
        'passwords' => 'encrypted:collection',
    ];

    public function __construct(array $attributes = [])
    {
        if (!isset($this->connection)) {
            $this->setConnection(config('mfa_otp.database_connection'));
        }

        $this->setTable(config('mfa_otp.table_name'));

        parent::__construct($attributes);
    }

    public function generatePasswords(): void
    {
        $count = config('mfa_otp.passwords.count', 10);
        $length = config('mfa_otp.passwords.length', 10);
        $passwords = new Collection();

        for ($i = 0; $i < $count; $i++) {
            $passwords->add(Mfa::randomString($length, self::ALPHABET));
        }
        $this->passwords = $passwords;
        $this->save();
    }
}
