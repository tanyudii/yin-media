<?php

namespace tanyudii\YinMedia\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use tanyudii\YinCore\Controllers\YinRestController;
use tanyudii\YinCore\Facades\YinRequestService;
use tanyudii\YinCore\Facades\YinResourceService;
use tanyudii\YinCore\Rules\ValidType;
use tanyudii\YinMedia\Models\Media;
use tanyudii\YinMedia\Resources\MediaResource;

class MediaController extends Controller
{
    use YinRestController {
        YinRestController::__construct as private __restConstruct;
    }

    public function __construct(Media $model)
    {
        $this->__restConstruct(
            $model,
            MediaResource::class,
            MediaResource::class
        );
    }

    /**
     * @param Request $request
     * @return JsonResource
     * @throws Exception
     */
    public function store(Request $request)
    {
        $isMultipleFile = is_array($request->file('file'));

        $request->validate(array_merge(YinRequestService::getDefaultRules(
            get_class($this->repository)),
            [
                "allowed_mimes" => ["nullable", "array"],
                "max_size" => ["nullable", "integer", "min:0"],
                "disk" => [
                    "required",
                    "string",
                    new ValidType(Config::get("yin-media.rules.allowed_disk", [])),
                ],
                "path" => ["required", "string"],
                ($isMultipleFile ? "file.*" : "file") => $this->getFileRules($request),
            ]
        ));

        try {
            DB::beginTransaction();

            $key = "file";
            $disk = $request->get("disk");
            $path = $request->get("path");

            $files = arr_strict($request->file($key));

            $data = Collection::make([]);

            foreach ($files as $index => $file) {
                $fileName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $encodedName = Carbon::now()->format("Y_m_d_his_") . strtoupper(Str::random());

                if ($extension) {
                    $encodedName .= "." . $extension;
                }

                $data->push($this->repository->create([
                    "name" => $fileName,
                    "encoded_name" => $encodedName,
                    "size" => $file->getSize(),
                    "extension" => $extension,
                    "path" => $file->storeAs($path, $encodedName, [
                        "disk" => $disk,
                    ]),
                    "disk" => $disk,
                ]));
            }

            DB::commit();

            if ($data->count() == 1) {
                return YinResourceService::jsonResource(MediaResource::class, $data->first());
            }

            return YinResourceService::jsonCollection(MediaResource::class, $data);
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    /**
     * @param Request $request
     * @return string[]
     */
    protected function getFileRules(Request $request)
    {
        $fileRules = ["required"];

        $allowedRuleFromRequest = Config::get(
            "yin-media.allow_rule_from_request",
            []
        );

        $mimes = in_array("max_size", $allowedRuleFromRequest)
            ? $request->get( "allowed_mimes", Config::get("yin-media.rules.allowed_mimes"))
            : Config::get("yin-media.rules.allowed_mimes");

        if (!is_null($mimes) && is_array($mimes)) {
            $fileRules[] =
                "mimes:" . (is_array($mimes) ? implode(",", $mimes) : $mimes);
        }

        $maxSize = in_array("max_size", $allowedRuleFromRequest)
            ? $request->get("max_size", Config::get("yin-media.rules.max_size"))
            : Config::get("yin-media.rules.max_size");

        if (!is_null($maxSize) && is_numeric($maxSize)) {
            $fileRules[] = "max:" . $maxSize;
        }

        return $fileRules;
    }
}
