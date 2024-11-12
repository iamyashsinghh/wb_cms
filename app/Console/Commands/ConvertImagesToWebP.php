<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use File;

class ConvertImagesToWebP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:convert-to-webp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert all images in the storage folder to WebP format';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // // Define the path to the storage folder
        // $storagePath = storage_path('app/public/images'); // Update this path to your specific folder

        // // Get all image files in the folder
        // $files = File::allFiles($storagePath);

        // // Loop through each file
        // foreach ($files as $file) {
        //     // Skip if the file is already in WebP format
        //     if ($file->getExtension() == 'webp') {
        //         continue;
        //     }

        //     $filePath = $file->getPathname();
        //     $fileNameWithoutExtension = pathinfo($filePath, PATHINFO_FILENAME);

        //     // Load the image using Intervention Image
        //     $image = Image::make($filePath);

        //     // Define the path for the new WebP image
        //     $webpPath = $file->getPath() . '/' . $fileNameWithoutExtension . '.webp';

        //     // Save the image as WebP
        //     $image->encode('webp', 90)->save($webpPath);

        //     // Log success
        //     $this->info('Converted: ' . $filePath . ' to WebP');
        // }

        // $this->info('Image conversion to WebP complete.');

        // return 0;
    }
}
