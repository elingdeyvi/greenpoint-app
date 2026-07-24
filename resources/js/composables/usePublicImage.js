import { computed } from 'vue';

/**
 * Composable Vue 3: resuelve URLs de imágenes públicas / storage.
 */
export function usePublicImage() {
    const resolveImage = (path) => {
        if (!path) {
            return null;
        }

        if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('/')) {
            return path;
        }

        if (path.startsWith('images/')) {
            return `/${path}`;
        }

        return `/storage/${path}`;
    };

    const imageStyle = (path) => {
        const url = resolveImage(path);
        return url ? { backgroundImage: `url(${url})` } : {};
    };

    return {
        resolveImage,
        imageStyle,
    };
}

/**
 * Helper reactivo para una prop/ruta concreta.
 */
export function useResolvedImage(pathRef) {
    const { resolveImage } = usePublicImage();
    return computed(() => resolveImage(typeof pathRef === 'function' ? pathRef() : pathRef?.value ?? pathRef));
}
