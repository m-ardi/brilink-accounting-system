import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            "colors": {
                "on-tertiary-fixed": "var(--color-on-tertiary-fixed)",
                "on-error-container": "var(--color-on-error-container)",
                "on-surface": "var(--color-on-surface)",
                "inverse-on-surface": "var(--color-inverse-on-surface)",
                "inverse-primary": "var(--color-inverse-primary)",
                "on-secondary-fixed": "var(--color-on-secondary-fixed)",
                "on-primary-fixed-variant": "var(--color-on-primary-fixed-variant)",
                "primary-fixed-dim": "var(--color-primary-fixed-dim)",
                "surface-bright": "var(--color-surface-bright)",
                "surface": "var(--color-surface)",
                "on-tertiary-fixed-variant": "var(--color-on-tertiary-fixed-variant)",
                "surface-container": "var(--color-surface-container)",
                "surface-dim": "var(--color-surface-dim)",
                "on-error": "var(--color-on-error)",
                "on-surface-variant": "var(--color-on-surface-variant)",
                "surface-tint": "var(--color-surface-tint)",
                "primary": "var(--color-primary)",
                "surface-container-lowest": "var(--color-surface-container-lowest)",
                "on-background": "var(--color-on-background)",
                "background": "var(--color-background)",
                "secondary": "var(--color-secondary)",
                "primary-container": "var(--color-primary-container)",
                "inverse-surface": "var(--color-inverse-surface)",
                "secondary-container": "var(--color-secondary-container)",
                "surface-container-high": "var(--color-surface-container-high)",
                "error-container": "var(--color-error-container)",
                "secondary-fixed": "var(--color-secondary-fixed)",
                "on-secondary": "var(--color-on-secondary)",
                "surface-container-highest": "var(--color-surface-container-highest)",
                "secondary-fixed-dim": "var(--color-secondary-fixed-dim)",
                "tertiary-fixed-dim": "var(--color-tertiary-fixed-dim)",
                "tertiary-container": "var(--color-tertiary-container)",
                "surface-variant": "var(--color-surface-variant)",
                "outline-variant": "var(--color-outline-variant)",
                "tertiary-fixed": "var(--color-tertiary-fixed)",
                "on-secondary-container": "var(--color-on-secondary-container)",
                "on-primary-container": "var(--color-on-primary-container)",
                "on-tertiary": "var(--color-on-tertiary)",
                "error": "var(--color-error)",
                "primary-fixed": "var(--color-primary-fixed)",
                "on-secondary-fixed-variant": "var(--color-on-secondary-fixed-variant)",
                "on-primary-fixed": "var(--color-on-primary-fixed)",
                "on-primary": "var(--color-on-primary)",
                "tertiary": "var(--color-tertiary)",
                "outline": "var(--color-outline)",
                "surface-container-low": "var(--color-surface-container-low)",
                "on-tertiary-container": "var(--color-on-tertiary-container)",
                "success-dim": "var(--color-success-dim)",
                "success-text": "var(--color-success-text)",
                "danger-dim": "var(--color-danger-dim)",
                "danger-text": "var(--color-danger-text)"
            },
            "borderRadius": {
                "DEFAULT": "0.125rem",
                "lg": "0.25rem",
                "xl": "0.5rem",
                "full": "0.75rem",
                "badge": "4px"
            },
            "spacing": {
                "gutter": "16px",
                "margin_sm": "8px",
                "topbar_height": "56px",
                "container_max_width": "1440px",
                "margin_md": "16px",
                "sidebar_width": "240px",
                "margin_lg": "24px"
            },
            "fontFamily": {
                "hero-figure": ["Inter", ...defaultTheme.fontFamily.sans],
                "body": ["Inter", ...defaultTheme.fontFamily.sans],
                "label-caps": ["Inter", ...defaultTheme.fontFamily.sans],
                "body-semibold": ["Inter", ...defaultTheme.fontFamily.sans],
                "headline-lg": ["Inter", ...defaultTheme.fontFamily.sans],
                "label": ["Inter", ...defaultTheme.fontFamily.sans],
                "headline-md": ["Inter", ...defaultTheme.fontFamily.sans],
                "subheading": ["Inter", ...defaultTheme.fontFamily.sans]
            },
            "fontSize": {
                "hero-figure": ["32px", { "lineHeight": "40px", "fontWeight": "600" }],
                "body": ["14px", { "lineHeight": "20px", "fontWeight": "400" }],
                "label-caps": ["12px", { "lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600" }],
                "body-semibold": ["14px", { "lineHeight": "20px", "fontWeight": "600" }],
                "headline-lg": ["24px", { "lineHeight": "32px", "fontWeight": "600" }],
                "label": ["12px", { "lineHeight": "16px", "fontWeight": "400" }],
                "headline-md": ["20px", { "lineHeight": "28px", "fontWeight": "600" }],
                "subheading": ["16px", { "lineHeight": "24px", "fontWeight": "600" }]
            }
        },
    },

    plugins: [forms, require('@tailwindcss/container-queries')],
};
