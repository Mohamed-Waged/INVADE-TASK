<?php

namespace App\Http\Resources;

use App\Models\Imageable;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthTokenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param array $request
     * @return array
       
     */
    public function toArray($request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'email'             => $this->email,
            'tokenType'         => 'bearer',
            'accessToken'       => $this->token,
            'expiresIn'         => auth('api')->factory()->getTTL() * 60
        ];
    }
}
