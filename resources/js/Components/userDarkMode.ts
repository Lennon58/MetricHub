import { ref, onMounted } from 'vue';

type ThemeMode = 'light' | 'dark' | 'system';

const theme = ref<ThemeMode>('system');

export function useDarkMode() {
    const applyTheme = (selectedTheme: ThemeMode) => {
        theme.value = selectedTheme;
        localStorage.setItem('theme', selectedTheme);

        const isDark =
            selectedTheme === 'dark' ||
            (selectedTheme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);

        if (isDark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    };

    const initTheme = () => {
        const savedTheme = (localStorage.getItem('theme') as ThemeMode) || 'system';
        applyTheme(savedTheme);

        // Ouve mudanças do sistema operacional se a opção for 'system'
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (theme.value === 'system') {
                if (e.matches) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            }
        });
    };

    return {
        theme,
        applyTheme,
        initTheme,
    };
}