export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './app/**/*.php',
    ],
    theme: {
        extend: {
            colors: {
                scada: {
                    bg: '#111827',
                    panel: '#1f2937',
                    panel2: '#0f172a',
                    accent: '#38bdf8',
                    success: '#22c55e',
                    warning: '#facc15',
                    danger: '#ef4444',
                    muted: '#94a3b8',
                },
            },
            boxShadow: {
                glow: '0 0 0 1px rgba(56, 189, 248, 0.15), 0 16px 40px rgba(0, 0, 0, 0.45)',
            },
        },
    },
    plugins: [],
};
