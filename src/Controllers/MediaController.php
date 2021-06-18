<?php

namespace tanyudii\YinMedia\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use tanyudii\YinMedia\Resources\MediaResource;
use tanyudii\YinMedia\Services\MediaService;

class MediaController extends Controller
{
    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    public function index(Request $request)
    {
        $data = $this->mediaService->findAll($request->all());

        return MediaResource::collection($data);
    }

    public function show(Request $request, $id)
    {
        $data = $this->mediaService->findOne(
            array_merge(
                [
                    "id" => $id,
                ],
                $request->all()
            )
        );

        if (empty($data)) {
            throw new ModelNotFoundException();
        }

        return new MediaResource($data);
    }

    public function store(Request $request)
    {
        $data = $this->mediaService->create($request->all());

        return $this->show($request, $data->id);
    }

    public function destroy(Request $request, $id)
    {
        $totalDeleted = $this->mediaService->delete(["id" => $id]);

        return response()->json([
            "success" => true,
            "data" => $totalDeleted,
        ]);
    }
}
