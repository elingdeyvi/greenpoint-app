<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CierreCajaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'caja_id' => ['required', 'exists:cajas,id'],
            'monto_final' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'caja_id.required' => 'La caja es obligatoria.',
            'monto_final.required' => 'El monto final reportado es obligatorio para el cierre.',
            'monto_final.numeric' => 'El monto final debe ser un número.',
            'monto_final.min' => 'El monto final no puede ser negativo.',
        ];
    }
}
