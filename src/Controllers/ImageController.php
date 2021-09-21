<?php

namespace tanyudii\YinMedia\Controllers;

use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Http\Request;
use League\Glide\Responses\LaravelResponseFactory;
use League\Glide\ServerFactory;

class ImageController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(FilesystemFactory $storage, Request $request, string $path)
    {
        $server = ServerFactory::create([
            'response' => new LaravelResponseFactory($request),
            'source' => $storage->disk('public')->getDriver(),
            'cache' => $storage->getDriver(),
            'cache_path_prefix' => '.cache',
        ]);

        $params = $request->all();
        if (strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false) {
            $params = array_merge(['fm' => 'webp'], $params);
        }

        return $server->getImageResponse($path,  $params);
    }
}