<?php

namespace Modules\Tasks\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestorRequest extends FormRequest
{
     /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'id'       => 'required'
        ];
    }

    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }
}
