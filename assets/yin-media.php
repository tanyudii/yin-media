<?php

return [
    /**
     * Namespace of models
     */
    "models" => [
        "media" => \tanyudii\YinMedia\Models\Media::class,
        "media_use" => \tanyudii\YinMedia\Models\MediaUse::class,
    ],

    /**
     * Request file rules
     */
    "rules" => [
        "allowed_disk" => array_keys(config("filesystems.disks")),

        /**
         * Set NULL for allow all Mime(s)
         */
        "allowed_mimes" => null,

        /**
         * Set NULL for allow all Size
         */
        "max_size" => null,
    ],

    "allow_rule_from_request" => ["allowed_mimes", "max_size"],

    /**
     * Route group config
     */
    "route" => [
        "prefix" => "media",
        "as" => "media.",
    ],
];
