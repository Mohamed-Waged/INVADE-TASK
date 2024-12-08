<?php

namespace Modules\Tasks\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
     /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'title'          => 'required',
            'description'    => 'nullable',
            'status'         => 'required|in:pending,completed',
            'categoryId'     => 'required|exists:categories,id'
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
