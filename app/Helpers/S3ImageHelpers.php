<?php

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

function S3ImageHelpers($s3FilePath, $resizedImage)
{
    Storage::disk('s3')->put($s3FilePath, $resizedImage);

    // $s3FileUrl = Storage::disk('s3')->url($s3FilePath);
    return $s3FilePath;
}

function s3ImageDelete($folderName, $imageName, $id)
{
    $imagePath = $imageName;
    $explodeArr = explode('/', $imagePath);
    $endIndex = end($explodeArr);
    Storage::disk('s3')->delete($folderName . $id . '/' . $endIndex . '');

    return true;
}

function imageResize($file, $width, $height)
{
    $resizedImage = Image::make($file)->resize($width, $height, function ($constraint) {
        $constraint->aspectRatio();
        $constraint->upsize();
    })->encode();

    return $resizedImage;
}