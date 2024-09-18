import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp, Link, Head } from '@inertiajs/inertia-vue3';
import {InertiaProgress} from "@inertiajs/progress";
import Layout from "./Shared/Layout";

createInertiaApp({
    resolve: name => {
        const page = require(`./Pages/${name}`).default
        if (page.layout === undefined && !name.startsWith('Session/')) {
            page.layout = Layout
        }
        return page
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .component('Link', Link)
            .component('Head', Head)
            .mount(el)
    },
    title: title => `${title} - Tarlac Agricultural University`,
});

InertiaProgress.init({
    delay: 250,
    color: '#348F6C',
    includeCSS: true,
    showSpinner: true,
})

