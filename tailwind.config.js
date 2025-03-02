import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    safelist: (() => {
        const colors = ['red', 'blue', 'green', 'pink', 'purple', 'yellow', 'orange', 'gray', 'teal', 'cyan'];
        const shades = ['400', '500', '600', '700', '800', '900'];

        return colors.flatMap(color => [
            ...shades.map(shade => `bg-${color}-${shade}`),    // Background
            ...shades.map(shade => `from-${color}-${shade}`),  // Gradienti "from"
            ...shades.map(shade => `to-${color}-${shade}`),    // Gradienti "to"
            ...shades.map(shade => `text-${color}-${shade}`),  // Colore del testo
            ...shades.map(shade => `ring-${color}-${shade}`)   // Bordo "ring"
        ]);
    })(),
    plugins: [],
};
