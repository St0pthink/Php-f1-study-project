<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CommentResource extends JsonResource
{
    public function toArray($request)
    {
        $authUser = Auth::user();
        $isFriend = false;
        $driverIsFriend = false;

        if ($authUser) {
            if ($this->user) {
                $isFriend = $authUser->isFriend($this->user);
            }
            if ($this->driver && $this->driver->user) {
                $driverIsFriend = $authUser->isFriend($this->driver->user);
            }
        }

        return [
            'id' => $this->id,
            'body' => $this->body,
            'user_id' => $this->user_id,
            'driver_id' => $this->driver_id,
            'author' => [
                'id' => $this->user->id ?? null,
                'name' => $this->user->name ?? null,
                'email' => $this->user->email ?? null,
            ],
            'is_friend' => $isFriend,
            'driver' => [
                'id' => $this->driver->id ?? null,
                'title' => $this->driver->title ?? null,
                'track_name' => $this->driver->track_name ?? null,
                'short_description' => $this->driver->short_description ?? null,
                'user_id' => $this->driver->user_id ?? null,
                'is_friend' => $driverIsFriend,
            ],
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
