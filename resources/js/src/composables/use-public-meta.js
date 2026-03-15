import { useHead } from '@vueuse/head';
import { computed, unref } from 'vue';

const SITE_NAME = 'GreenPoint';

function plainText(str, maxLen = 320) {
  if (!str || typeof str !== 'string') return '';
  const stripped = str.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
  return maxLen ? stripped.slice(0, maxLen) : stripped;
}

/**
 * Meta tags y título para el sitio público.
 * @param {Object|import('vue').ComputedRef<Object>} source - Objeto o computed con { title?, description?, keywords? } (o meta_descripcion, meta_keywords del API).
 *   En vistas con datos del API (Nosotros, Historia, etc.) pasar un computed que use la respuesta.
 */
export function usePublicMeta(source) {
  useHead(
    computed(() => {
      const data = unref(source) || {};
      const title = data.title ?? '';
      const description = plainText(data.description ?? data.meta_descripcion ?? '');
      const keywords = data.keywords ?? data.meta_keywords ?? '';

      const pageTitle = title ? `${title} | ${SITE_NAME}` : SITE_NAME;

      const meta = [
        description && { name: 'description', content: description },
        keywords && { name: 'keywords', content: keywords },
        { property: 'og:title', content: pageTitle },
        description && { property: 'og:description', content: description },
      ].filter(Boolean);

      return {
        title: pageTitle,
        meta,
      };
    })
  );
}
