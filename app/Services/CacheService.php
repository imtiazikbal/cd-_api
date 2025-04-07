<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService {

    public function cacheClear(array $cacheKeys): bool
    {
       foreach($cacheKeys as $cacheKey){
        if(Cache::has($cacheKey)){
            Cache::forget($cacheKey);
            return true;
        }
       }
    }

}