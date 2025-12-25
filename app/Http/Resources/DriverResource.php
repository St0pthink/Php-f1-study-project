<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class DriverResource extends JsonResource
{
    public function toArray($request)
    {
        $authUser = Auth::user();
        $isFriend = false;

        if ($authUser && $this->user) {
            $isFriend = $authUser->isFriend($this->user);
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'track_name' => $this->track_name,
            'short_description' => $this->short_description,
            'details_html' => $this->details_html,
            'image_path' => $this->image_path,
            'image_url' => $this->image_url,
            'user_id' => $this->user_id,
            'user' => [
                'id' => $this->user->id ?? null,
                'name' => $this->user->name ?? null,
                'email' => $this->user->email ?? null,
            ],
            'is_friend' => $isFriend,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
