<?php

namespace tanyudii\YinMedia\Models;

use tanyudii\YinCore\Database\Eloquent\ModelUuid;

class MediaUse extends ModelUuid
{
    protected $fillable = ["media", "subject_type", "subject_id"];

    public function media()
    {
        return $this->belongsTo(config("yin-media.models.media"));
    }

    public function subject()
    {
        return $this->morphTo();
    }
}
