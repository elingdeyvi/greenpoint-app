import { ref } from 'vue';

/**
 * Composable mínimo para estado de caja.
 * 
 * Por ahora solo expone una bandera `cajaAbierta` y
 * una función `loadCajaAbierta` que no hace nada real,
 * pero permite que el layout compile correctamente.
 * 
 * Cuando se implemente la lógica de cajas, aquí se puede
 * reemplazar para consultar el estado real desde la API.
 */
export function useCaja() {
    const cajaAbierta = ref(true);

    const loadCajaAbierta = async () => {
        // TODO: Reemplazar con llamada real a API si es necesario
        return cajaAbierta.value;
    };

    return {
        cajaAbierta,
        loadCajaAbierta,
    };
}

