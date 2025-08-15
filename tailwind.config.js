import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    safelist: [
        'backdrop-blur-sm', // tambahkan ini
        'backdrop-blur-md',
        'backdrop-blur-lg'
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Gabarito", ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
