<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResizeImageManipulationRequest;
use App\Http\Requests\StoreImageManipulationRequest;
use App\Http\Requests\UpdateImageManipulationRequest;
use App\Models\Album;
use App\Models\ImageManipulation;
// use Faker\Core\File;
use File;
use Illuminate\Support\Str;
use Faker\Provider\File as ProviderFile;
use GuzzleHttp\Psr7\UploadedFile;
use Illuminate\Http\UploadedFile as HttpUploadedFile;
use Illuminate\Support\Facades\File as FacadesFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile as FileUploadedFile;
use Intervention\Image\ImageManager;
use Intervention\Image\ImageManagerStatic as Image;

class ImageManipulationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
    public function byAlbum(Album $album)
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreImageManipulationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function resize(ResizeImageManipulationRequest $request)
    {
        $all = $request->all();
        /** @var UploadedFile|string $image */
        $image = $all['image'];
        unset($all['image']);
        $data = [
            'type' => ImageManipulation::TYPE_RESIZE,
            'data' => json_encode($all),
            'user_id' => null,
        ];
        if (isset($all['album_id'])) {
            // TODO
            $data['album_id'] = $all['album_id'];
        }

        $dir = 'images/' . Str::random() . '/';
        $absoutPath = public_path($dir);
        FacadesFile::makeDirectory($absoutPath);

        // images/dash2
        if ($image instanceof FileUploadedFile) {
            $data['name'] = $image->getClientOriginalName();
            // test.jpg =test-resize.jpg
            $filename = pathinfo($data['name'] . PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $originalPath = $absoutPath . $data['name'];
            $image->move($absoutPath, $data['name']);
        } else {
            $data['name'] = pathinfo($image, PATHINFO_BASENAME);
            $filename = pathinfo($image, PATHINFO_FILENAME);
            $extension = pathinfo($image, PATHINFO_EXTENSION);
            $originalPath = $absoutPath . $data['name'];
            copy($image, $originalPath);
        }
        $data['path'] = $dir . $data['name'];
        $w = $all['w'];
        $h = $all['h'] ?? false;

        list($width, $height) =  $this->getImageWidthAndHeight($w, $h, $originalPath);
        var_dump($width, $height);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ImageManipulation  $imageManipulation
     * @return \Illuminate\Http\Response
     */
    public function show(ImageManipulation $imageManipulation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ImageManipulation  $imageManipulation
     * @return \Illuminate\Http\Response
     */
    public function destroy(ImageManipulation $imageManipulation)
    {
        //
    }
    public function getImageWidthAndHeight($width, $height, string $originalPath)
    {
        // 1000 . 50% =>500px
        // Image
        // Image

        // and you are ready to go ...
        $image = Image::make($originalPath);
        $orinalWIdth = $image->width();
        $originalHeight = $image->height();
        if (str_ends_with($width, '%')) {
            $rationWidth = (float)  str_replace('%', '', $width);
            $rationHeight = $height ? (float)  str_replace('%', '', $height) : $rationWidth;

            $newWidth = $orinalWIdth * $rationWidth / 100;
            $newHeight = $originalHeight * $rationHeight / 100;
        } else {
            $newWidth = (float)$width;
            $newHeight = $height ? (float) $height : $originalPath * $newWidth / $orinalWIdth;
        }

        return [$newHeight, $newHeight];
    }
}
