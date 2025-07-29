<?php

namespace App\Services;
use Illuminate\Support\Facades\Storage;
use Spatie\Image\Image;

class ImageService{

    public function resizeImage($originalPath, $width, $height)
    {
        // Generate temporary file path
        $random = rand();
        $tempFilePath = Storage::disk('public')->path('images/items/thumbnail/'.$random.'.png');

        // Load the image
        $image = Image::load(Storage::disk('public')->path($originalPath));
        
        // Resize and save to temporary file
        $image->width($width)->height($height)->save($tempFilePath);

        // Create data URI from temporary file
        $imageContent = file_get_contents($tempFilePath);
        $dataUri = 'data:' . mime_content_type($tempFilePath) . ';base64,' . base64_encode($imageContent);

        // Remove temporary file
        unlink($tempFilePath);

        return $dataUri;
    }
}
