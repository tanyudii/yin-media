<?php

namespace tanyudii\YinMedia\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use tanyudii\YinCore\Repositories\ServiceRepository;
use tanyudii\YinMedia\Exceptions\YinMediaException;

class MediaService extends ServiceRepository
{
    public function __construct()
    {
        parent::__construct(app(config("yin-media.models.media")));
    }

    /**
     * @param $payload
     * @return array|string[][]
     * @throws ValidationException
     */
    public function createRules($payload)
    {
        return array_merge($this->defaultRules(), [
            "disk" => [
                "required",
                "string",
                "in:" .
                implode(",", config("yin-media.rules.allowed_disk", [])),
            ],
            "path" => ["required", "string"],
            "file" => $this->getFileRules($payload),
        ]);
    }

    /**
     * @param $payload
     * @throws YinMediaException
     */
    public function beforeCreate(&$payload)
    {
        $request = request();
        $key = "file";
        $disk = $payload["disk"];
        $path = $payload["path"];

        $files = $request->file($key);
        if (!is_array($files)) {
            $files = [$files];
        }

        foreach ($files as $index => $file) {
            if ($index > 0) {
                break;
            }

            $fileName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $encodedName =
                now()->format("Y_m_d_his_") . strtoupper(Str::random());

            if ($extension) {
                $encodedName .= "." . $extension;
            }

            $payload = [
                "name" => $fileName,
                "encoded_name" => $encodedName,
                "size" => $file->getSize(),
                "extension" => $extension,
                "path" => $file->storeAs($path, $encodedName, [
                    "disk" => $disk,
                ]),
                "disk" => $disk,
            ];
        }

        if (empty($payload)) {
            throw new YinMediaException(
                "Whoops, Error in file when uploading to storage."
            );
        }
    }

    /**
     * @param $payload
     * @return string[]
     * @throws ValidationException
     */
    protected function getFileRules($payload)
    {
        $fileRules = ["required"];

        $allowedRuleFromRequest = config(
            "yin-media.allow_rule_from_request",
            []
        );

        $ruleValidateRequest = [
            "allowed_mimes" => ["nullable", "array"],
            "max_size" => ["nullable", "integer", "min:0"],
        ];

        $validator = Validator::make($payload, $ruleValidateRequest);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $mimes = in_array("max_size", $allowedRuleFromRequest)
            ? Arr::get($payload, "allowed_mimes")
            : config("yin-media.rules.allowed_mimes");

        if (!is_null($mimes) && is_array($mimes)) {
            $fileRules[] =
                "mimes:" . (is_array($mimes) ? implode(",", $mimes) : $mimes);
        }

        $maxSize = in_array("max_size", $allowedRuleFromRequest)
            ? Arr::get($payload, "max_size")
            : config("yin-media.rules.max_size");

        if (!is_null($maxSize) && is_numeric($maxSize)) {
            $fileRules[] = "max:" . $maxSize;
        }

        return $fileRules;
    }

    /**
     * @param $model
     * @param $payload
     */
    public function afterDelete($model, &$payload)
    {
        foreach ($model as $item) {
            Storage::disk($item->disk)->delete($item->path);
        }
    }
}
