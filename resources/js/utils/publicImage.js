/**
 * Compat: reexporta el helper Vue 3 desde el composable.
 * Preferir: import { usePublicImage } from '@/composables/usePublicImage'
 */
import { usePublicImage } from '@/composables/usePublicImage';

const { resolveImage } = usePublicImage();

export function resolvePublicImage(path) {
    return resolveImage(path);
}
