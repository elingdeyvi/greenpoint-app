<template>
  <div class="venta-ticket-preview">
    <div class="ticket-actions no-print">
      <button type="button" class="btn btn-outline-secondary btn-sm" @click="cerrar">
        {{ $t('pos_cerrar') }}
      </button>
      <button type="button" class="btn btn-primary btn-sm" @click="imprimir">
        {{ $t('pos_imprimir') }}
      </button>
    </div>

    <div ref="ticketRef" class="ticket-document">
      <header class="ticket-header">
        <h2 class="ticket-folio">{{ venta.folio }}</h2>
        <p class="ticket-sucursal">{{ venta.sucursal?.nombre || '' }}</p>
      </header>

      <table class="ticket-tabla">
        <thead>
          <tr>
            <th>{{ $t('pos_ticket_producto') }}</th>
            <th class="text-end">{{ $t('pos_cantidad') }}</th>
            <th class="text-end">{{ $t('pos_ticket_precio_unit') }}</th>
            <th class="text-end">{{ $t('pos_ticket_subtotal') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="d in lineas" :key="d.id || d.producto_id">
            <td>{{ nombreProducto(d) }}</td>
            <td class="text-end">{{ Number(d.cantidad_pedida).toFixed(2) }}</td>
            <td class="text-end">$ {{ Number(precioUnit(d)).toFixed(2) }}</td>
            <td class="text-end">$ {{ subtotal(d).toFixed(2) }}</td>
          </tr>
        </tbody>
      </table>

      <div class="ticket-totales">
        <div class="ticket-total-row">
          <span>{{ $t('pos_total') }}</span>
          <strong>$ {{ Number(venta.total).toFixed(2) }}</strong>
        </div>
      </div>

      <div v-if="venta.pagos && venta.pagos.length" class="ticket-pagos">
        <h4 class="ticket-pagos-titulo">{{ $t('pos_pagos') }}</h4>
        <ul class="ticket-pagos-lista">
          <li v-for="(p, idx) in venta.pagos" :key="idx">
            {{ metodoPagoLabel(p.metodo_pago) }}: $ {{ Number(p.monto).toFixed(2) }}
          </li>
        </ul>
      </div>

      <div v-if="venta.qr_payload" class="ticket-qr">
        <img
          :src="urlQr"
          :alt="$t('pos_codigo_qr')"
          width="160"
          height="160"
          class="ticket-qr-img"
        />
        <p class="ticket-qr-leyenda">{{ $t('pos_escanear_macuspana') }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  venta: {
    type: Object,
    required: true,
    default: () => ({}),
  },
});

const emit = defineEmits(["cerrar"]);

const lineas = computed(() => props.venta.detalles || []);

function nombreProducto(d) {
  return d.producto?.nombre ?? d.nombre ?? d.producto_id ?? "—";
}

function precioUnit(d) {
  return d.producto?.precio_unitario ?? d.precio_unitario ?? 0;
}

function subtotal(d) {
  const cant = Number(d.cantidad_pedida) || 0;
  const pu = precioUnit(d);
  return cant * Number(pu);
}

const urlQr = computed(() => {
  const payload = props.venta.qr_payload || "";
  if (!payload) return "";
  return `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(payload)}`;
});

function metodoPagoLabel(metodo) {
  const labels = {
    efectivo: "Efectivo",
    tarjeta: "Tarjeta",
    transferencia: "Transferencia",
    credito: "Crédito",
  };
  return labels[metodo] || metodo || "—";
}

function imprimir() {
  window.print();
}

function cerrar() {
  emit("cerrar");
}
</script>

<style scoped>
.venta-ticket-preview {
  max-width: 400px;
  margin: 0 auto;
}
.ticket-actions {
  display: flex;
  gap: 0.5rem;
  justify-content: flex-end;
  margin-bottom: 1rem;
}
.ticket-document {
  background: #fff;
  border: 1px solid #dee2e6;
  border-radius: 8px;
  padding: 1.25rem;
  font-size: 0.9rem;
  color: #212529;
}
.ticket-header {
  text-align: center;
  border-bottom: 1px dashed #dee2e6;
  padding-bottom: 0.75rem;
  margin-bottom: 1rem;
}
.ticket-folio {
  font-size: 1.25rem;
  font-weight: 700;
  margin: 0 0 0.25rem 0;
  color: #212529;
}
.ticket-sucursal {
  margin: 0;
  color: #495057;
  font-size: 0.85rem;
}
.ticket-tabla {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 1rem;
  color: #212529;
}
.ticket-tabla th,
.ticket-tabla td {
  padding: 0.35rem 0.5rem;
  border-bottom: 1px solid #eee;
  color: #212529;
}
.ticket-tabla th {
  font-size: 0.75rem;
  text-transform: uppercase;
  color: #495057;
}
.ticket-total-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem 0;
  border-top: 2px solid #212529;
  font-size: 1rem;
  color: #212529;
}
.ticket-pagos {
  margin-top: 1rem;
  padding-top: 0.75rem;
  border-top: 1px dashed #dee2e6;
  color: #212529;
}
.ticket-pagos-titulo {
  font-size: 0.85rem;
  margin: 0 0 0.5rem 0;
  color: #212529;
}
.ticket-pagos-lista {
  list-style: none;
  padding: 0;
  margin: 0;
  font-size: 0.85rem;
  color: #212529;
}
.ticket-pagos-lista li {
  color: #212529;
}
.ticket-qr {
  margin-top: 1rem;
  text-align: center;
}
.ticket-qr-img {
  display: block;
  margin: 0 auto 0.5rem;
}
.ticket-qr-leyenda {
  font-size: 0.75rem;
  color: #6c757d;
  margin: 0;
}
</style>

<style>
@media print {
  body * {
    visibility: hidden;
  }
  .venta-ticket-preview,
  .venta-ticket-preview * {
    visibility: visible;
    color: #000 !important;
  }
  .venta-ticket-preview .ticket-sucursal,
  .venta-ticket-preview .ticket-tabla th,
  .venta-ticket-preview .ticket-qr-leyenda {
    color: #333 !important;
  }
  .venta-ticket-preview {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    max-width: 100%;
    margin: 0;
    padding: 0;
    border: none;
    background: #fff !important;
  }
  .no-print {
    display: none !important;
  }
}
</style>
