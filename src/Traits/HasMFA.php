<?php

declare(strict_types=1);

namespace Wirgen\LaravelMfa\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Wirgen\LaravelMfa\Providers\AbstractProvider;

trait HasMFA
{
    public function getProvider(string $type): AbstractProvider
    {
        $mfaClass = config('mfa.types')[$type];
        return new $mfaClass();
    }

    /**
     * @throws Exception
     */
    public function register(string $type): array
    {
        if (!$this instanceof Model) {
            throw new Exception("Subject must be Model instance");
        }

        return $this->getProvider($type)->register($this);
    }

    /**
     * @throws Exception
     */
    public function unregister(string $type): void
    {
        if (!$this instanceof Model) {
            throw new Exception("Subject must be Model instance");
        }

        $this->getProvider($type)->unregister($this);
    }

    /**
     * @throws Exception
     */
    public function enable(string $type): bool
    {
        if (!$this instanceof Model) {
            throw new Exception("Subject must be Model instance");
        }

        return $this->getProvider($type)->enable($this);
    }

    /**
     * @throws Exception
     */
    public function regenerate(string $type): array
    {
        if (!$this instanceof Model) {
            throw new Exception("Subject must be Model instance");
        }

        return $this->getProvider($type)->regenerate($this);
    }

    /**
     * @throws Exception
     */
    public function verify(string $type, string $payload): bool
    {
        if (!$this instanceof Model) {
            throw new Exception("Subject must be Model instance");
        }

        return $this->getProvider($type)->verify($this, $payload);
    }
}
