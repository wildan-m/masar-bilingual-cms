import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            // Downloaded from fonts.bunny.net at build time and served from
            // this site, so visitors make no third-party font requests.
            fonts: [
                bunny('IBM Plex Sans', {
                    alias: 'plex-sans',
                    weights: [400, 500, 600, 700],
                    subsets: ['latin'],
                    preload: [{ weight: 400 }, { weight: 600 }],
                    optimizedFallbacks: false,
                }),
                bunny('IBM Plex Sans Arabic', {
                    alias: 'plex-arabic',
                    weights: [400, 500, 600, 700],
                    subsets: ['arabic'],
                    preload: [{ weight: 400 }, { weight: 600 }],
                    optimizedFallbacks: false,
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
