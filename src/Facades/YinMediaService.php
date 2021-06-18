<?php

namespace tanyudii\YinMedia\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array routes()
 *
 * @see \tanyudii\YinMedia\Services\YinMediaService
 */
class YinMediaService extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return "yin-media-service";
    }
}
