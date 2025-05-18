import './bootstrap';
import Alpine from 'alpinejs';
import './dark-mode';

window.Alpine = Alpine;

Alpine.start();

// Initialize Preline components when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', () => {
    // Define a helper function to initialize components
    const initializePreline = () => {
        if (typeof window.HSOverlay !== 'undefined') {
            window.HSOverlay.init();
        }
        
        if (typeof window.HSDropdown !== 'undefined') {
            window.HSDropdown.init();
        }
        
        if (typeof window.HSCollapse !== 'undefined') {
            window.HSCollapse.init();
        }
        
        if (typeof window.HSTabs !== 'undefined') {
            window.HSTabs.init();
        }
        
        if (typeof window.HSMegaMenu !== 'undefined') {
            window.HSMegaMenu.init();
        }
        
        if (typeof window.HSStepForm !== 'undefined') {
            window.HSStepForm.init();
        }
        
        if (typeof window.HSRemoveElement !== 'undefined') {
            window.HSRemoveElement.init();
        }
        
        if (typeof window.HSThemeSwitcher !== 'undefined') {
            window.HSThemeSwitcher.init();
        }
        
        console.log('Preline components initialized successfully');
    };
    
    // Try to initialize Preline
    try {
        initializePreline();
    } catch (error) {
        console.error('Error initializing Preline components:', error);
    }
});