// Load Laravel's bootstrap and Alpine.js
import "./bootstrap";
import Alpine from "alpinejs";

// Preline UI v2 import
import "preline/dist/preline.js";

window.Alpine = Alpine;
Alpine.start();

// Initialize Preline and dark mode once DOM is ready
document.addEventListener("DOMContentLoaded", () => {
    initializeTheme();         // Setup dark/light mode toggle
    initializePreline();       // Core Preline components
});

// === PRELINE INITIALIZATION ===

function initializePreline() {
    try {
        // Preline v2 recommended way
        if (
            typeof window.HSStaticMethods !== "undefined" &&
            typeof window.HSStaticMethods.autoInit === "function"
        ) {
            window.HSStaticMethods.autoInit();
            console.log("✅ Preline initialized via HSStaticMethods.autoInit()");
        } else {
            console.warn("⚠️ Preline autoInit not found. Using manual fallback.");
            initializePrelineManually();
        }
    } catch (error) {
        console.error("❌ Error initializing Preline:", error);
        initializePrelineManually();
    }
}

function initializePrelineManually() {
    const components = [
        { name: "HSDropdown", selector: "[data-hs-dropdown]" },
        { name: "HSAccordion", selector: "[data-hs-accordion]" },
        { name: "HSTooltip", selector: "[data-hs-tooltip]" },
        { name: "HSTab", selector: "[data-hs-tab]" },
        { name: "HSOverlay", selector: "[data-hs-overlay]" },
    ];

    components.forEach(({ name, selector }) => {
        const Constructor = window[name];
        if (Constructor) {
            document.querySelectorAll(selector).forEach(el => {
                try {
                    new Constructor(el);
                } catch (e) {
                    console.error(`Failed to init ${name} on`, el, e);
                }
            });
        }
    });
}

// === THEME TOGGLING (LIGHT / DARK) ===

function initializeTheme() {
    const theme = localStorage.getItem("hs_theme") || "auto";
    const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;

    if (theme === "dark" || (theme === "auto" && prefersDark)) {
        document.documentElement.classList.add("dark");
        document.documentElement.classList.remove("light");
    } else {
        document.documentElement.classList.remove("dark");
        document.documentElement.classList.add("light");
    }

    const themeToggle = document.getElementById("theme-toggle");
    if (themeToggle) {
        themeToggle.addEventListener("click", () => {
            const isDark = document.documentElement.classList.contains("dark");
            if (isDark) {
                document.documentElement.classList.remove("dark");
                document.documentElement.classList.add("light");
                localStorage.setItem("hs_theme", "light");
            } else {
                document.documentElement.classList.remove("light");
                document.documentElement.classList.add("dark");
                localStorage.setItem("hs_theme", "dark");
            }
        });
    }

    // Listen to system preference change
    window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change", e => {
        if ((localStorage.getItem("hs_theme") || "auto") === "auto") {
            if (e.matches) {
                document.documentElement.classList.add("dark");
                document.documentElement.classList.remove("light");
            } else {
                document.documentElement.classList.remove("dark");
                document.documentElement.classList.add("light");
            }
        }
    });
}

// Utility: call this after dynamic content load
window.refreshPreline = function () {
    setTimeout(() => {
        initializePreline();
    }, 100);
};
