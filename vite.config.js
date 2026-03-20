import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { viteStaticCopy } from 'vite-plugin-static-copy';

export default defineConfig({
    css: {
        preprocessorOptions: {
            scss: {
                // Silence legacy @import deprecation warnings from Bootstrap/vendor SCSS.
                // Remove once all imports are migrated to @use/@forward (Dart Sass 3.x).
                silenceDeprecations: ['import', 'global-builtin', 'color-functions', 'if-function'],
            },
        },
    },
    plugins: [
        laravel({
            input: ['resources/scss/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
        viteStaticCopy({
            targets: [
                {
                    src: 'node_modules/bootstrap-icons/font/fonts/*',
                    dest: 'assets/fonts',
                },
            ],
        }),
    ],
});
