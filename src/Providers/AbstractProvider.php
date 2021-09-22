<?php

declare(strict_types=1);

namespace Wirgen\LaravelMfa\Providers;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Wirgen\LaravelMfa\Models\Mfa;

abstract class AbstractProvider
{
    public $mfaType;

    /**
     * Steps to initialize a two-factor authentication method
     *
     * @throws Exception
     */
    public function __construct()
    {
        if (!$this->mfaType) {
            throw new Exception("Mfa type is not defined");
        }

        $mfaTypes = config('mfa.types');

        if (!isset($mfaTypes[$this->mfaType])) {
            throw new Exception("$this->mfaType is not registered");
        }
    }

    /**
     * Registering a new two-factor authentication method for a subject
     *
     * @param Model $subject
     * @return array
     * @throws Exception
     */
    public function register(Model $subject): array
    {
        $mfaModel = Mfa::getMfaModel($subject);
        $mfaModel->save();

        return [];
    }

    /**
     * Removing a two-factor authentication method
     *
     * @param Model $subject
     * @throws Exception
     */
    public function unregister(Model $subject): void
    {
        $mfaModel = Mfa::getMfaModel($subject);
        $mfaModel->delType($this->mfaType);
    }

    /**
     * Enabling a two-factor authentication method
     *
     * @param Model $subject
     * @return bool
     * @throws Exception
     */
    public function enable(Model $subject): bool
    {
        $mfaModel = Mfa::getMfaModel($subject);
        $mfaModel->addType($this->mfaType);

        return true;
    }

    /**
     * @throws Exception
     */
    public function regenerate(Model $subject): array
    {
        return [];
    }

    /**
     * Payload validation for two-factor authentication
     *
     * @param Model $subject
     * @param string $payload
     * @return bool
     * @throws Exception
     */
    public function verify(Model $subject, string $payload): bool
    {
        return true;
    }
}
