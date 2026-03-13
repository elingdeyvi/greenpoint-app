<?php

namespace App\Http\Requests;

use App\Models\ConfiguracionEmpresa;
use App\Models\Producto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validación para crear una venta (POS).
 * Reglas según ANALISIS_LOGICA_NEGOCIO.md §2.2 y §2.3:
 * - Donativos: observaciones obligatorias; pagos no exigidos.
 * - Ventas normales: si se envían pagos, la suma debe coincidir con el total (validado en controlador).
 */
class StoreVentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->sucursal_id === null || $this->sucursal_id === '') {
            $config = ConfiguracionEmpresa::obtenerConfiguracion();
            $this->merge(['sucursal_id' => $config?->sucursal_id]);
        }
    }

    public function rules(): array
    {
        $isDonativo = $this->isDonativo();

        return [
            'sucursal_id' => ['required', 'exists:sucursales,id'],
            'cliente_id' => ['nullable', 'exists:clientes,id'],
            'tipo' => ['nullable', 'in:venta,donativo'],
            'es_donativo' => ['nullable', 'boolean'],
            'observaciones' => [
                Rule::requiredIf($isDonativo),
                'nullable',
                'string',
                'max:1000',
            ],
            'detalles' => ['required', 'array', 'min:1'],
            'detalles.*.producto_id' => ['required', 'exists:productos,id'],
            'detalles.*.cantidad_pedida' => ['required', 'numeric', 'min:0.01'],
            'detalles.*.precio_unitario' => ['required', 'numeric', 'min:0'],
            'pagos' => ['nullable', 'array'],
            'pagos.*.metodo_pago' => ['required_with:pagos', 'string', 'max:30'],
            'pagos.*.monto' => ['required_with:pagos', 'numeric', 'min:0.01'],
            'pagos.*.monto_recibido' => ['nullable', 'numeric', 'min:0'],
            'pagos.*.referencia_pago' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'sucursal_id.required' => 'La sucursal es obligatoria. Configure la sucursal de venta en Configuración de la empresa.',
            'sucursal_id.exists' => 'La sucursal seleccionada no es válida.',
            'detalles.required' => 'Debe incluir al menos un detalle de producto.',
            'detalles.min' => 'Debe incluir al menos un detalle de producto.',
            'detalles.*.precio_unitario.required' => 'El precio unitario es obligatorio para cada detalle (solo lectura desde catálogo).',
            'observaciones.required' => 'Las observaciones son obligatorias para donativos (justificación).',
            'pagos.*.metodo_pago.required_with' => 'Cada pago debe tener un método de pago.',
            'pagos.*.monto.required_with' => 'Cada pago debe tener un monto.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $detalles = $this->input('detalles', []);
            foreach ($detalles as $idx => $detalle) {
                $producto = Producto::find($detalle['producto_id'] ?? 0);
                if (! $producto) {
                    continue;
                }
                $precioDb = (float) $producto->precio_unitario;
                $precioEnv = (float) ($detalle['precio_unitario'] ?? 0);
                if (abs($precioDb - $precioEnv) > 0.005) {
                    $validator->errors()->add(
                        "detalles.{$idx}.precio_unitario",
                        "El precio unitario debe coincidir con el registrado para el producto ({$producto->nombre}): \$ " . number_format($precioDb, 2) . '.'
                    );
                }
            }
        });
    }

    /** Donativo si tipo === 'donativo' o es_donativo = true (según §2.3). */
    public function isDonativo(): bool
    {
        return $this->input('tipo') === 'donativo' || $this->boolean('es_donativo');
    }
}
