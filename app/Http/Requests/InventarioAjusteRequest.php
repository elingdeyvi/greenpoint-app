<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventarioAjusteRequest extends FormRequest
{
    /**
     * Authorize the request. Permission is checked in the controller.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for inventory adjustment.
     */
    public function rules(): array
    {
        return [
            'producto_id' => ['required', 'exists:productos,id'],
            'tipo' => ['required', 'in:incremento,decremento,establecer'],
            'valor' => ['required', 'numeric', 'min:0'],
            'reason' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Custom messages in Spanish for business context.
     */
    public function messages(): array
    {
        return [
            'producto_id.required' => 'El producto es obligatorio.',
            'producto_id.exists' => 'El producto seleccionado no existe.',
            'tipo.required' => 'El tipo de ajuste es obligatorio (incremento, decremento o establecer).',
            'tipo.in' => 'El tipo de ajuste debe ser incremento, decremento o establecer.',
            'valor.required' => 'El valor es obligatorio.',
            'valor.numeric' => 'El valor debe ser numérico.',
            'valor.min' => 'El valor debe ser mayor o igual a cero.',
            'reason.required' => 'La observación es obligatoria para registrar el ajuste.',
        ];
    }
}

