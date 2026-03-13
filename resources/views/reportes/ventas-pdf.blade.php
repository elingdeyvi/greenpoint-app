<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de ventas</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        h1 { font-size: 14px; margin-bottom: 4px; }
        .meta { color: #555; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background: #f5f5f5; }
        .text-right { text-align: right; }
        .footer { margin-top: 16px; font-size: 9px; color: #666; }
    </style>
</head>
<body>
    <h1>{{ $nombreEmpresa ?? 'Reporte de ventas' }}</h1>
    <p class="meta">
        Generado: {{ now()->format('d/m/Y H:i') }}
        @if(!empty($fechaDesde) || !empty($fechaHasta))
            | Período: {{ $fechaDesde ? \Carbon\Carbon::parse($fechaDesde)->format('d/m/Y') : '—' }}
            al {{ $fechaHasta ? \Carbon\Carbon::parse($fechaHasta)->format('d/m/Y') : '—' }}
        @endif
    </p>
    <table>
        <thead>
            <tr>
                <th>Folio</th>
                <th>Sucursal</th>
                <th>Usuario</th>
                <th class="text-right">Total</th>
                <th>Estatus</th>
                <th>Tipo</th>
                <th>Observaciones</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventas as $v)
            <tr>
                <td>{{ $v->folio }}</td>
                <td>{{ $v->sucursal->nombre ?? '—' }}</td>
                <td>{{ $v->usuario->name ?? '—' }}</td>
                <td class="text-right">{{ number_format($v->total, 2) }}</td>
                <td>{{ $v->estatus }}</td>
                <td>{{ ($v->tipo === 'donativo' || $v->es_donativo) ? 'Donativo' : 'Venta' }}</td>
                <td>{{ $v->observaciones }}</td>
                <td>{{ $v->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="7">Sin registros en el período.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($ventas->isNotEmpty())
    @php
        $ventasIngreso = $ventas->filter(fn($v) => !($v->es_donativo ?? false) && ($v->tipo ?? 'venta') !== 'donativo');
        $ventasDonativo = $ventas->filter(fn($v) => ($v->es_donativo ?? false) || ($v->tipo ?? 'venta') === 'donativo');
    @endphp
    <p class="footer">
        Total de ventas (ingreso): {{ $ventasIngreso->count() }} |
        Suma ingresos: {{ number_format($ventasIngreso->sum('total'), 2) }}<br>
        Salidas por donativo: {{ $ventasDonativo->count() }} |
        Valor donativos (referencial): {{ number_format($ventasDonativo->sum('total'), 2) }}
    </p>
    @endif
</body>
</html>
