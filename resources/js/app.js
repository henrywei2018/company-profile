import './bootstrap';
import Alpine from 'alpinejs';
// Import Preline properly
import 'preline/dist/preline.js';

window.Alpine = Alpine;
Alpine.start();

// Initialize Preline UI components
document.addEventListener('DOMContentLoaded', function() {
    initializePreline();
    initializeTheme();
});

// Initialize Preline after page load
function initializePreline() {
    try {
        // First check for the newest way Preline initializes
        if (typeof window.HSStaticMethods !== 'undefined' && typeof window.HSStaticMethods.autoInit === 'function') {
            window.HSStaticMethods.autoInit();
            console.log('Preline initialized via HSStaticMethods.autoInit()');
        } 
        // For older versions of Preline
        else if (typeof window.HSAccordion === 'object') {
            console.log('Found HSAccordion object:', window.HSAccordion);
            // Use our custom initializer which is more tolerant
            initializePrelineManually();
        }
        // For even older versions
        else {
            console.log('No Preline methods found, checking if it was loaded');
            // Check if Preline was loaded at all
            if (typeof window.Preline !== 'undefined') {
                console.log('Found Preline object:', window.Preline);
                // Try to initialize Preline using its own method
                if (typeof window.Preline.init === 'function') {
                    window.Preline.init();
                    console.log('Preline initialized via Preline.init()');
                }
            } else {
                console.warn('Preline not found in window. Check if it loaded properly.');
                // Add a fallback here if necessary for the accordion
                initializeAccordionFallback();
            }
        }
    } catch (error) {
        console.error('Error initializing Preline components:', error);
        // Try fallback
        initializeAccordionFallback();
    }
}

// Manual initialization for components
function initializePrelineManually() {
    console.log('Manually initializing Preline components');
    
    const components = [
        { name: 'HSAccordion', selector: '[data-hs-accordion]' },
        { name: 'HSDropdown', selector: '[data-hs-dropdown]' },
        { name: 'HSOverlay', selector: '[data-hs-overlay]' },
        { name: 'HSTooltip', selector: '[data-hs-tooltip]' },
        { name: 'HSCollapse', selector: '[data-hs-collapse]' },
        { name: 'HSTab', selector: '[data-hs-tab]' }
    ];
    
    components.forEach(component => {
        if (window[component.name]) {
            try {
                const elements = document.querySelectorAll(component.selector);
                if (elements.length && typeof window[component.name].init === 'function') {
                    window[component.name].init(elements);
                    console.log(`Initialized ${component.name} on ${elements.length} elements`);
                }
            } catch (err) {
                console.error(`Error initializing ${component.name}:`, err);
            }
        } else {
            console.warn(`${component.name} not found in window object`);
        }
    });
    
    // Also try to initialize any older Preline components that might use a different pattern
    if (typeof window.HSCore !== 'undefined') {
        try {
            window.HSCore.components.HSTabs.init('[data-hs-tab]');
            console.log('Initialized HSCore tabs');
        } catch (err) {
            console.warn('Error initializing HSCore tabs:', err);
        }
    }
}

// Fallback implementation for accordions
function initializeAccordionFallback() {
    console.log('Using fallback accordion implementation');
    const accordionToggles = document.querySelectorAll('.hs-accordion-toggle');
    
    // Only run this if there are accordion toggles on the page
    if (accordionToggles.length > 0) {
        console.log(`Found ${accordionToggles.length} accordion toggles, adding manual handling`);
        
        accordionToggles.forEach(toggle => {
            // Only add event listener if it doesn't already have one
            toggle.removeEventListener('click', handleAccordionToggle);
            toggle.addEventListener('click', handleAccordionToggle);
        });
        
        // Initialize accordions that should be open by default
        document.querySelectorAll('.hs-accordion').forEach(accordion => {
            const content = accordion.querySelector('.hs-accordion-content');
            if (content && !content.classList.contains('hidden')) {
                accordion.classList.add('active');
                const toggle = accordion.querySelector('.hs-accordion-toggle');
                if (toggle) toggle.setAttribute('aria-expanded', 'true');
            }
        });
    }
}

// Handler function for accordion toggle
function handleAccordionToggle() {
    const accordion = this.closest('.hs-accordion');
    const content = accordion.querySelector('.hs-accordion-content');
    
    if (!accordion || !content) return;
    
    const isActive = accordion.classList.contains('active');
    
    // Close all accordions in the same group if not set to always open
    const accordionGroup = this.closest('.hs-accordion-group');
    if (accordionGroup && !accordionGroup.hasAttribute('data-hs-accordion-always-open')) {
        accordionGroup.querySelectorAll('.hs-accordion').forEach(acc => {
            if (acc !== accordion && acc.classList.contains('active')) {
                acc.classList.remove('active');
                const accContent = acc.querySelector('.hs-accordion-content');
                const accToggle = acc.querySelector('.hs-accordion-toggle');
                
                if (accContent) {
                    accContent.style.height = '0';
                    setTimeout(() => {
                        accContent.classList.add('hidden');
                    }, 200);
                }
                
                if (accToggle) {
                    accToggle.setAttribute('aria-expanded', 'false');
                }
            }
        });
    }
    
    // Toggle current accordion
    if (isActive) {
        accordion.classList.remove('active');
        content.style.height = '0';
        this.setAttribute('aria-expanded', 'false');
        setTimeout(() => {
            content.classList.add('hidden');
        }, 200);
    } else {
        accordion.classList.add('active');
        content.classList.remove('hidden');
        content.style.height = content.scrollHeight + 'px';
        this.setAttribute('aria-expanded', 'true');
    }
}

// Refresh Preline components after dynamic content changes
window.refreshPreline = function() {
    setTimeout(function() {
        initializePreline();
    }, 100);
};

// Initialize theme based on localStorage or system preference
function initializeTheme() {
    // Check if theme preference exists in localStorage
    const storedTheme = localStorage.getItem('hs_theme') || 'auto';
    
    // Apply theme based on stored preference or system preference
    if (storedTheme === 'dark' || (storedTheme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
        document.documentElement.classList.remove('light');
    } else {
        document.documentElement.classList.remove('dark');
        document.documentElement.classList.add('light');
    }
    
    // Set up theme toggle button
    const themeToggleBtn = document.getElementById('theme-toggle');
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function() {
            // Toggle between light and dark mode
            if (document.documentElement.classList.contains('dark')) {
                // Switch to light mode
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
                localStorage.setItem('hs_theme', 'light');
            } else {
                // Switch to dark mode
                document.documentElement.classList.remove('light');
                document.documentElement.classList.add('dark');
                localStorage.setItem('hs_theme', 'dark');
            }
        });
    }
}

// Handle system theme preference changes
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
    const storedTheme = localStorage.getItem('hs_theme');
    if (storedTheme === 'auto' || !storedTheme) {
        if (e.matches) {
            document.documentElement.classList.add('dark');
            document.documentElement.classList.remove('light');
        } else {
            document.documentElement.classList.remove('dark');
            document.documentElement.classList.add('light');
        }
    }
});