<?php

namespace tanyudii\YinMedia\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use tanyudii\YinMedia\Controllers\ImageController;
use tanyudii\YinMedia\Controllers\MediaController;

class YinMediaService
{
    /**
     * Register media route
     */
    public function routes()
    {
        Route::group(Config::get("yin-media.route", []), function () {
            Route::get("/", [MediaController::class, "index"])->name('index');
            Route::post("/", [MediaController::class, "store"])->name('store');
            Route::get("/{id}", [MediaController::class, "show"])->name('show');
            Route::delete("/{id}", [MediaController::class, "destroy"])->name('destroy');
        });

        Route::group(Config::get("yin-media.route_image", []), function () {
            Route::get("/{path:.+}", [ImageController::class, "show"]);
        });
    }

    /**
     * @param $model
     * @param string $relationName
     */
    public function logUse($model, string $relationName)
    {
        if ($attachment = $model->$relationName) {
            $logUse = [
                "entity" => get_class($model),
                "subject_id" => $model->id,
            ];

            if (
                !$attachment
                    ->mediaUses()
                    ->where($logUse)
                    ->exists()
            ) {
                $attachment->mediaUses()->create($logUse);
            }
        }
    }
}
