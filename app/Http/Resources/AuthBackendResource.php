<?php

namespace App\Http\Resources;

use App\Models\Imageable;
use Modules\Roles\Entities\Permission;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthBackendResource extends JsonResource
{
    public function toArray($request): array
    {

        return [
            'userData' => [
                'id'            => $this->id,
                'encryptId'     => encrypt($this->id),
                'fullName'      => $this->name,
                'email'         => $this->email,
            ],

            'accessToken'       => $this->token,
        ];
    }
}
