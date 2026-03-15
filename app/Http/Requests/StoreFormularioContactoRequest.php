<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreFormularioContactoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'asunto' => ['nullable', 'string', 'max:255'],
            'mensaje' => ['required', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede superar los 255 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no es válido.',
            'email.max' => 'El correo electrónico no puede superar los 255 caracteres.',
            'telefono.max' => 'El teléfono no puede superar los 50 caracteres.',
            'asunto.max' => 'El asunto no puede superar los 255 caracteres.',
            'mensaje.required' => 'El mensaje es obligatorio.',
            'mensaje.max' => 'El mensaje no puede superar los 5000 caracteres.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'email' => 'correo electrónico',
            'telefono' => 'teléfono',
            'asunto' => 'asunto',
            'mensaje' => 'mensaje',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Los datos enviados no son válidos.',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
