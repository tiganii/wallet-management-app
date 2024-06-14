<?php

namespace App\Http\Requests;

use App\Traits\HasResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Validation\Rules\Password;

class RegisterUserRequest extends FormRequest
{
    use HasResponse;
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:15',
            'email' => 'required|email|unique:users',
            'password' => ['required','confirmed',Password::min(8)->mixedCase()->numbers()],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->sendResponse(Response::HTTP_BAD_REQUEST, ['success'=>true,'message'=>'Validation Errors', 'errors'=>$validator->errors()])
        );
    }

    public function messages(): array{
        return [

        ];
    }
}
