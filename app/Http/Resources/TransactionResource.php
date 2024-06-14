<?php

namespace App\Http\Resources;

use App\Services\HashIdService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'type' => $this->type,
            'amount' => $this->amount,
            'date' => $this->created_at,
            'status' => $this->status,
        ];
    }
}
