<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrgRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Assuming 'id' is the route parameter for the organisation being updated
        // This allows the 'name' to be unique except for the current organisation being updated
        // Allows updating while ensuring uniqueness (excluding the current organisation being edited).
        return [
            'name' => 'required|string|max:255|unique:organisations,name,' . $this->route('id'),
        ];
    }
}
