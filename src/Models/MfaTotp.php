<?php

declare(strict_types=1);

namespace Wirgen\LaravelMfa\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class MfaTotp
 * @package Wirgen\LaravelMfa\Models
 *
 * @property int $id
 * @property int $mfa_id
 * @property string $secret
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class MfaTotp extends Model
{
    public const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    protected $fillable = ['mfa_id'];

    protected $casts = [
        'secret' => 'encrypted',
    ];

    public function __construct(array $attributes = [])
    {
        if (!isset($this->connection)) {
            $this->setConnection(config('mfa_totp.database_connection'));
        }

        $this->setTable(config('mfa_totp.table_name'));

        parent::__construct($attributes);
    }

    public function generateSecret(): void
    {
        $this->secret = Mfa::randomString(config('mfa_totp.secret_length', 16), self::ALPHABET);
        $this->save();
    }
}
