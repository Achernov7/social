import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

import { resolve, dirname } from 'path';
import { fileURLToPath, URL } from 'url';
import Vue18nPlugin from '@intlify/unplugin-vue-i18n/vite';


export default defineConfig({
    // uncomment for docker
    // _____________________________________________
    // server: {
    //     host: '0.0.0.0',
    //     hmr: {
    //         host: 'localhost'
    //     },
    //     watch: {
    //         usePolling: true
    //     }
    // },
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        Vue18nPlugin({
            runtimeOnly: false,
            include: resolve(dirname(fileURLToPath(import.meta.url)), './resources/js/i18n/locales/**'),
        })
           
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
        }
    },
});
