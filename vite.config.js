import { defineConfig } from 'vite';

export default defineConfig({
    build: {
        outDir: 'dist',
        emptyOutDir: true,
        rollupOptions: {
            input: 'resources/js/datepicker.js',
            output: {
                format: 'iife',
                entryFileNames: 'datepicker.js',
                name: 'LivewireDatepicker',
            },
        },
    },
});
