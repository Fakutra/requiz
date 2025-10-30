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
        'backdrop-blur-sm',
        'backdrop-blur-md',
        'backdrop-blur-lg',
        'bg-[#EFFEFF]',
        'border-[#009DA9]'
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