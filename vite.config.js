import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import path from "path";

export default defineConfig({
    // server: {
    //     hmr: {
    //         host: "eos.test",
    //     },
    // },
    server: {
        hmr: {
            // host: '192.168.1.24',
            host: "127.0.0.1",
        },
    },
    plugins: [
        vue(),
        laravel([
            "resource/scss/app.scss",
            "resources/js/app.js",
            // 'resource/scss/dashboard.scss',
            // 'resources/js/dashboard.js',
        ]),
    ],
    resolve: {
        alias: {
            vue: "vue/dist/vue.esm-bundler.js",
            "~bootstrap": path.resolve(__dirname, "node_modules/bootstrap"),
            "@": "/resources/js",
        },
    },
});
