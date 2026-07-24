import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

/**
 * Acceso tipado a props compartidas del sitio público (config, redes, contactos).
 */
export function usePublicSite() {
    const page = usePage();

    const config = computed(() => page.props.publicSite?.config ?? {});
    const redesSociales = computed(() => page.props.publicSite?.redesSociales ?? []);
    const contactos = computed(() => page.props.publicSite?.contactos ?? []);

    const getConfig = (clave, fallback = '') => {
        const value = config.value?.[clave];
        return value !== undefined && value !== null && value !== '' ? value : fallback;
    };

    const sitioNombre = computed(() => getConfig('sitio_nombre', 'GreenPoint'));
    const empresaDescripcion = computed(() => getConfig('empresa_descripcion', ''));
    const telefonoPrincipal = computed(() => getConfig('telefono_principal', ''));
    const emailPrincipal = computed(() => getConfig('email_principal', ''));
    const direccionMatriz = computed(() => getConfig('direccion_matriz', ''));
    const whatsapp = computed(() => getConfig('whatsapp', ''));
    const whatsappUrl = computed(() => {
        const phone = String(whatsapp.value || '').replace(/\D/g, '');
        return phone ? `https://api.whatsapp.com/send?phone=${phone}` : null;
    });
    const horarioLunesViernes = computed(() => getConfig('horario_lunes_viernes', ''));
    const horarioSabado = computed(() => getConfig('horario_sabado', ''));
    const horarioDomingo = computed(() => getConfig('horario_domingo', ''));

    return {
        config,
        redesSociales,
        contactos,
        getConfig,
        sitioNombre,
        empresaDescripcion,
        telefonoPrincipal,
        emailPrincipal,
        direccionMatriz,
        whatsapp,
        whatsappUrl,
        horarioLunesViernes,
        horarioSabado,
        horarioDomingo,
    };
}
