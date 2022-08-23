<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserProfilePictureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $path = Storage::disk('profile_pictures')->url($this->file->path);

        return [
            'id'   => $this->id,
            'path' => $path,
        ];
    }
}
