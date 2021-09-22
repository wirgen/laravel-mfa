<?php

declare(strict_types=1);

namespace Wirgen\LaravelMfa\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Class Mfa
 * @package Wirgen\LaravelMfa\Models
 *
 * @property int $id
 * @property int $subject_id
 * @property Collection $types
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @method static self firstOrNew($id)
 */
class Mfa extends Model
{
    protected $fillable = ['subject_id'];

    protected $casts = [
        'types' => 'collection',
    ];

    public function __construct(array $attributes = [])
    {
        if (!isset($this->connection)) {
            $this->setConnection(config('mfa.database_connection'));
        }

        $this->setTable(config('mfa.table_name'));

        parent::__construct($attributes);
    }

    /**
     * @throws Exception
     */
    public static function getMfaModel(Model $subject): self
    {
        $mfaModel = self::firstOrNew(['subject_id' => $subject->getKey()]);
        if (!$mfaModel->exists) {
            $mfaModel->types = new Collection();
        }

        return $mfaModel;
    }

    public static function randomString(
        $length = 16,
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'
    ): string {
        mt_srand();
        $alphabetSize = mb_strlen($alphabet);
        $shuffled = str_shuffle($alphabet);
        $string = '';

        while (mb_strlen($string) < $length) {
            try {
                $start = random_int(0, $alphabetSize);
            } catch (Exception $e) {
                /** @noinspection RandomApiMigrationInspection */
                $start = mt_rand(0, $alphabetSize);
            }
            $string .= mb_substr($shuffled, $start, 1);
        }

        return $string;
    }

    public function addType($type): void
    {
        $types = $this->types->values();
        $this->types = $types->add($type)->sort()->unique()->values();
        $this->save();
    }

    public function delType($type): void
    {
        $types = $this->types->values();
        $this->types = $types->diff($type)->values();
        $this->save();
    }
}
