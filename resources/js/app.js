import "../css/app.css";
import "./bootstrap";

import { createApp, h } from "vue";
import { createInertiaApp, Head, Link } from "@inertiajs/vue3";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import Layout from "./Pages/Layouts/Layout.vue";
import VueMaplibreGl from "@indoorequal/vue-maplibre-gl";
import { register } from "swiper/element/bundle";
// register Swiper custom elements
register();

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.vue", { eager: true });
        let page = pages[`./Pages/${name}.vue`];
        page.default.layout = page.default.layout || Layout;
        return page;
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.config.globalProperties.formatCurrency = function (value) {
            return new Intl.NumberFormat("en-PH", {
                style: "currency",
                currency: "PHP",
            }).format(value);
        };

        app.use(plugin)
            .use(ZiggyVue)
            .use(VueMaplibreGl)
            .component("Head", Head)
            .component("Link", Link)
            .mount(el);
    },
});
