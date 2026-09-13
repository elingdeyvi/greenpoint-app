<script setup>
import { watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: '',
    },
    size: {
        type: String,
        default: 'lg', // sm | lg | xl
    },
    closeable: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['close']);

watch(
    () => props.show,
    (open) => {
        document.body.classList.toggle('modal-open', open);
    },
);

const close = () => {
    if (props.closeable) {
        emit('close');
    }
};
</script>

<template>
    <Teleport to="body">
        <template v-if="show">
            <div class="modal-backdrop fade show" @click="close" />
            <div
                class="modal fade show d-block"
                tabindex="-1"
                role="dialog"
                aria-modal="true"
                @keydown.esc.prevent="close"
            >
                <div class="modal-dialog modal-dialog-scrollable" :class="`modal-${size}`">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ title }}</h5>
                            <button
                                v-if="closeable"
                                type="button"
                                class="btn-close"
                                aria-label="Cerrar"
                                @click="close"
                            />
                        </div>
                        <div class="modal-body">
                            <slot />
                        </div>
                        <div v-if="$slots.footer" class="modal-footer">
                            <slot name="footer" />
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Teleport>
</template>
