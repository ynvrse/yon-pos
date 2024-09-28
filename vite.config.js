import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/css/luvi-ui.css", 
                "resources/css/filepond.css", 
                "resources/js/app.js",
                "resources/js/filepond.js",
            ],
            refresh: true,
        }),
    ],
});
