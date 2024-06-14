<?php

namespace App\Http\Resources;

use App\Services\HashIdService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (new HashIdService())->encode($this->id),
            'name' => $this->name,
            'email' => $this->email,
            'balance' => $this->wallet->amount
        ];
    }
}
