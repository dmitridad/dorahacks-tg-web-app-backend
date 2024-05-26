<?php

namespace App\Auth\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class TmaUserProvider extends EloquentUserProvider
{
    /**
     * @inheritDoc
     */
    public function retrieveByCredentials(array $credentials)
    {
        $tgUserId = $credentials['tg_user_id'] ?? null;
        if (!$tgUserId) {
            return null;
        }

        return $this->newModelQuery()->where('tg_user_id', $tgUserId)->first();
    }

    public function validateCredentials(UserContract $user, array $credentials): bool
    {
        if (empty($credentials['tg_user_id'])) {
            return false;
        }

        return true;
    }
}
