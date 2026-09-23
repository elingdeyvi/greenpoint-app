<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import PageHero from '@/Components/Public/PageHero.vue';

const props = defineProps({
    pagina: {
        type: Object,
        default: null,
    },
});

const secciones = computed(() => props.pagina?.secciones ?? []);
const pageTitle = computed(() => props.pagina?.titulo || 'Aviso de privacidad');

/**
 * cgi-bin: la mayoría de “títulos” van en <strong> dentro del <p>;
 * solo "Medios para ejercer los derechos ARCO." es un <h3>.
 */
const isBlockHeading = (titulo) => {
    if (!titulo) {
        return false;
    }
    return !titulo.includes('.-') && /[.]$/.test(titulo.trim());
};

const escapeHtml = (value = '') =>
    String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');

/** Bold "Etiqueta.- " at the start of the string or after a newline (cgi-bin pattern). */
const boldLegalLabels = (text = '') =>
    escapeHtml(text).replace(/(^|\n)([^.\n]{1,160}?\.-\s)/g, '$1<strong>$2</strong>');

const splitLead = (contenido = '') => {
    const text = String(contenido);

    const greenpoint = text.match(/^(GREENPOINT S\.A\. de C\.V\.)([\s\S]*)$/);
    if (greenpoint) {
        return { strong: greenpoint[1], rest: greenpoint[2] };
    }

    const labeled = text.match(/^([^\n]+?\.-)\s*([\s\S]*)$/);
    if (labeled) {
        return { strong: labeled[1], rest: labeled[2] ? ` ${labeled[2]}` : '' };
    }

    return { strong: null, rest: text };
};

const sectionView = (seccion) => {
    const heading = isBlockHeading(seccion.titulo);
    const inlineTitle = seccion.titulo && !heading ? seccion.titulo : null;
    const lead = inlineTitle ? null : splitLead(seccion.contenido || '');
    const listas = seccion.listas ?? [];
    const hasText = !!(seccion.contenido || inlineTitle);

    let bodyHtml = '';
    if (inlineTitle) {
        bodyHtml = boldLegalLabels(seccion.contenido ? ` ${seccion.contenido}` : '');
    } else if (lead?.strong) {
        bodyHtml = `<strong>${escapeHtml(lead.strong)}</strong>${boldLegalLabels(lead.rest || '')}`;
    } else {
        bodyHtml = boldLegalLabels(lead?.rest || '');
    }

    return {
        heading: heading ? seccion.titulo : null,
        inlineTitle,
        bodyHtml,
        listas,
        hasText,
        paragraphClass: listas.length ? 'mb-4' : 'mb-0',
    };
};

const contactEmail = computed(() => {
    const last = [...secciones.value].reverse().find((s) => s.contenido);
    const match = last?.contenido?.match(/[\w.+-]+@greenpoint\.mx/i);
    return match?.[0] || 'contacto@greenpoint.mx';
});
</script>

<template>
    <Head :title="pageTitle" />

    <PublicLayout>
        <template v-if="pagina">
            <PageHero
                :title="pageTitle"
                :breadcrumbs="[
                    { label: 'Inicio', href: route('public.home') },
                    { label: 'Nosotros' },
                    { label: 'Aviso de Privacidad' },
                ]"
                background="/images/demo/page-title/con1.jpg"
            />

            <!-- cgi-bin aviso.html — PRIVACY POLICY -->
            <section>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-12">
                            <div
                                class="p-1-6 p-md-2-2 border border-color-extra-light-gray border-radius-10"
                            >
                                <template v-if="secciones.length">
                                    <div
                                        v-for="(seccion, sIndex) in secciones"
                                        :key="seccion.id"
                                        :class="
                                            sIndex === secciones.length - 1
                                                ? ''
                                                : 'mb-1-6 mb-lg-1-9 mb-xl-2-5'
                                        "
                                    >
                                        <template
                                            v-for="view in [sectionView(seccion)]"
                                            :key="`${seccion.id}-view`"
                                        >
                                            <h3 v-if="view.heading" class="mb-3 h5">
                                                {{ view.heading }}
                                            </h3>

                                            <p
                                                v-if="view.hasText"
                                                :class="view.paragraphClass"
                                                style="text-align: justify; white-space: pre-line"
                                                v-html="
                                                    view.inlineTitle
                                                        ? `<strong>${escapeHtml(view.inlineTitle)}</strong>${view.bodyHtml}`
                                                        : view.bodyHtml
                                                "
                                            />

                                            <ul
                                                v-if="view.listas.length"
                                                class="list-style1 mb-0"
                                                :class="{ 'mt-3': view.hasText || view.heading }"
                                            >
                                                <li
                                                    v-for="(lista, index) in view.listas"
                                                    :key="lista.id ?? index"
                                                >
                                                    <i
                                                        class="ti-check text-primary me-3 font-weight-600"
                                                    ></i>
                                                    {{ lista.texto }}
                                                </li>
                                            </ul>
                                        </template>
                                    </div>

                                    <div class="mt-3">
                                        <a
                                            class="text-primary font-weight-500"
                                            :href="`mailto:${contactEmail}`"
                                        >
                                            {{ contactEmail }}
                                        </a>
                                    </div>
                                </template>
                                <p v-else class="text-muted text-center py-4 mb-0">
                                    Aún no se ha publicado el contenido de este documento.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </template>

        <section v-else class="gp-section text-center">
            <div class="container">
                <i class="fa-solid fa-file-shield fs-1 text-gp-primary mb-3"></i>
                <h1 class="h3">Contenido no disponible</h1>
                <p class="text-muted mb-0">
                    La información de esta sección aún no ha sido publicada.
                </p>
            </div>
        </section>
    </PublicLayout>
</template>
