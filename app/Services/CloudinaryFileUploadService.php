<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class CloudinaryFileUploadService{

     public function __construct(public Client $client)
    {
    }

    public function uploadFile(Request $request, string $fileKey ,string $folder, string $uploadPreset){
           $response = $this->client->post('https://api.cloudinary.com/v1_1/dyhmwzlpe/raw/upload', [
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => fopen($request->file($fileKey)->getRealPath(), 'r'),
                        'filename' => $request->file('resume')->getClientOriginalName(),
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
