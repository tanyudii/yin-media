<?php

namespace tanyudii\YinMedia\Models;

use Illuminate\Support\Facades\Storage;
use tanyudii\YinCore\Contracts\WithDefaultOrderCreatedAt;
use tanyudii\YinCore\Contracts\WithDefaultOrderDesc;
use tanyudii\YinCore\Contracts\WithRelationRequest;
use tanyudii\YinCore\Database\Eloquent\ModelUuid;

class Media extends ModelUuid implements
    WithRelationRequest,
    WithDefaultOrderCreatedAt,
    WithDefaultOrderDesc
{
    protected $fillable = [
        "name",
        "encoded_name",
        "size",
        "extension",
        "path",
        "disk",
    ];

    public function mediaUses()
    {
        return $this->hasMany(config("yin-media.models.media_use"));
    }

    public function getUrlAttribute()
    {
        return Storage::disk($this->disk)->url($this->path);
    }
}
