document.addEventListener('DOMContentLoaded', function() {
    // Dark Mode Toggle
    const initHSDarkMode = () => {
        const $darkModeTogglers = document.querySelectorAll('[data-hs-theme-click-value]');
        const $html = document.querySelector('html');
        
        // Check for stored theme preference or system preference
        const storedTheme = localStorage.getItem('hs_theme');
        if (storedTheme) {
            if (storedTheme === 'dark') {
                $html.classList.add('dark');
            } else {
                $html.classList.remove('dark');
            }
            
            // Update togglers based on stored theme
            updateTogglers(storedTheme);
        } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            $html.classList.add('dark');
            updateTogglers('dark');
        }
        
        // Add click events to all togglers
        $darkModeTogglers.forEach($toggler => {
            $toggler.addEventListener('click', () => {
                const theme = $toggler.getAttribute('data-hs-theme-click-value');
                
                localStorage.setItem('hs_theme', theme);
                
                if (theme === 'dark') {
                    $html.classList.add('dark');
                } else {
                    $html.classList.remove('dark');
                }
                
                // Update togglers based on new theme
                updateTogglers(theme);
            });
        });
        
        // Function to update toggler visibility
        function updateTogglers(theme) {
            document.querySelectorAll('[data-hs-theme-click-value="dark"]').forEach(el => {
                if (theme === 'dark') {
                    el.classList.add('hidden');
                } else {
                    el.classList.remove('hidden');
                }
            });
            
            document.querySelectorAll('[data-hs-theme-click-value="default"]').forEach(el => {
                if (theme === 'dark') {
                    el.classList.remove('hidden');
                } else {
                    el.classList.add('hidden');
                }
            });
        }
    };

    // Call the initialization function
    initHSDarkMode();
});