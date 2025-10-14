<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class CloudinaryFileUploadService{

     public function __construct(public Client $client)
    {
    }

    public function uploadFile(Request $request = null,
    string $fileKey = null,
    string $path = null,
    string $folder = null,
    string $uploadPreset = null){
           $response = $this->client->post('https://api.cloudinary.com/v1_1/dyhmwzlpe/raw/upload', [
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => !$path ? fopen($request->file($fileKey)->getRealPath(), 'r') : fopen($path, 'r') ,
                        'filename' =>  !$path ? $request->file('resume')->getClientOriginalName() : basename($path),
                    ],
                    [
                        'name' => 'resource_type',
                        'contents' => 'raw',
                    ],
                    [
                        'name' => 'upload_preset',
                        'contents' => $uploadPreset, // if unsigned upload
                    ],
                    [
                        'name' => 'folder',
                        'contents' => $folder,
                    ],
                ],
            ]);

            return json_decode($response->getBody(), true);

    }
}
