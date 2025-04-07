<?php

namespace App\Traits;

use App\Models\MerchantToken;
use App\Models\User;

trait merchantCheck
{
    public function isAuthenticated($id, $token)
    {
        try {
            if (MerchantToken::query()->where('user_id', $id)
                ->where('token', $token)
                ->count()) {
                return $this->getMerchant($id);
            }

            return false;
        } catch (\Exception $exception) {
            return false;
        }
    }

    private function getMerchant($id)
    {
        return User::query()->findOrFail($id);
    }
}
