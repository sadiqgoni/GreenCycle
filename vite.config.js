import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
            'resources/css/app.css',
            'resources/css/filament/admin/theme.css',
            'resources/css/filament/company/theme.css',
            'resources/css/filament/household/theme.css',
            'resources/js/app.js',
        ],
            refresh: true,
        }),
    ],
});
