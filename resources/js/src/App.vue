<template>
    <div :class="containerClasses">
        <component v-bind:is="layout"></component>
    </div>
</template>
<script setup>
    import { computed, onMounted } from "vue";

    import "./assets/sass/app.scss";
    import "./assets/sass/public-site.scss";

    import { useMeta } from "./composables/use-meta";
    import { useStore } from "vuex";
    import * as ConfiguracionEmpresaRepository from "@/repositories/ConfiguracionEmpresaRepository";

    useMeta({ title: "Sales Admin" });

    const store = useStore();

    const layout = computed(() => {
        return store.getters.layout;
    });

    const containerClasses = computed(() => {
        const classes = [];
        if (store.state.layout_style) {
            classes.push(store.state.layout_style);
        }
        if (store.state.menu_style) {
            // Si layout_style es "full" y menu_style es "vertical", usar "collapsible-vertical"
            if (store.state.layout_style === "full" && store.state.menu_style === "vertical") {
                classes.push("collapsible-vertical");
            } else {
                classes.push(store.state.menu_style);
            }
        }
        return classes;
    });

    // Cargar sucursal activa una sola vez al iniciar la app y definir perfil de interfaz
    onMounted(async () => {
        try {
            const res = await ConfiguracionEmpresaRepository.getSucursal();
            const suc = res?.data ?? res;
            const tipo = suc?.tipo_sucursal || null;
            let perfil = null;
            if (tipo === "venta") {
                perfil = "VENTA";
            } else if (tipo === "venta_almacen") {
                perfil = "VENTA_ALMACEN";
            }
            store.commit("setSucursalPerfil", { tipo, perfil });
        } catch (e) {
            store.commit("setSucursalPerfil", { tipo: null, perfil: null });
        }
    });
</script>
<script>
    // layouts
    import appLayout from "./layouts/app-layout.vue";
    import authLayout from "./layouts/auth-layout.vue";
    import publicLayout from "./layouts/public-layout.vue";

    export default {
        components: {
            app: appLayout,
            auth: authLayout,
            public: publicLayout,
        },
    };
</script>
