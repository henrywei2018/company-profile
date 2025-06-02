<!-- resources/views/components/admin/chat-quick-responses.blade.php -->
<div class="flex flex-wrap gap-2" id="quick-responses-container">
    <div id="templates-loading" class="text-xs text-gray-500 dark:text-gray-400" style="display: none;">
        Loading templates...
    </div>
    <div id="quick-responses-list" class="flex flex-wrap gap-2">
        <button type="button" onclick="insertQuickResponse('Thank you for contacting us. How can I help you today?')" class="quick-response-btn">
            Quick: Greeting
        </button>
        <button type="button" onclick="insertQuickResponse('I understand your concern. Let me check that for you.')" class="quick-response-btn">
            Quick: Acknowledge
        </button>
        <button type="button" onclick="insertQuickResponse('Is there anything else I can help you with today?')" class="quick-response-btn">
            Quick: Follow-up
        </button>
    </div>

    <div class="w-full mt-2">
        <div class="relative">
            <input type="text" id="template-search" placeholder="Search templates..." class="w-full text-xs px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" autocomplete="off">
            <div id="template-search-results" class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-32 overflow-y-auto hidden"></div>
        </div>
    </div>

    <div class="w-full mt-2">
        <div class="flex flex-wrap gap-1">
            <button type="button" onclick="loadTemplatesByType('quick_reply')" class="template-category-btn active" data-type="quick_reply">Quick Replies</button>
            <button type="button" onclick="loadTemplatesByType('greeting')" class="template-category-btn" data-type="greeting">Greetings</button>
            <button type="button" onclick="loadTemplatesByType('auto_response')" class="template-category-btn" data-type="auto_response">Auto Responses</button>
            <button type="button" onclick="loadTemplatesByType('offline')" class="template-category-btn" data-type="offline">Offline</button>
        </div>
    </div>
</div>

@push('styles')
<style>
.quick-response-btn {
    @apply inline-flex items-center px-2 py-1 border border-gray-300 dark:border-gray-600 shadow-sm text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors;
}
.template-category-btn {
    @apply px-2 py-1 text-xs rounded border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors;
}
.template-category-btn.active {
    @apply bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 border-blue-300 dark:border-blue-600;
}
.template-search-item {
    @apply px-3 py-2 text-xs hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer border-b border-gray-200 dark:border-gray-600 last:border-b-0;
}
.template-search-item:hover {
    @apply bg-blue-50 dark:bg-blue-900/20;
}
</style>
@endpush

@push('scripts')
<script>
// Chat Templates Handler
class ChatTemplatesHandler {
    constructor() {
        this.templates = [];
        this.currentType = 'quick_reply';
        this.searchTimeout = null;
        this.init();
    }
    
    init() {
        this.loadDefaultTemplates();
        this.setupEventListeners();
        
        // Load quick_reply templates by default
        setTimeout(() => {
            this.loadTemplatesByType('quick_reply');
        }, 500);
    }
    
    setupEventListeners() {
        // Template search
        const searchInput = document.getElementById('template-search');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    this.searchTemplates(e.target.value);
                }, 300);
            });
            
            // Hide search results when clicking outside
            document.addEventListener('click', (e) => {
                if (!e.target.closest('#template-search') && !e.target.closest('#template-search-results')) {
                    this.hideSearchResults();
                }
            });
        }
    }
    
    async loadDefaultTemplates() {
        try {
            const response = await fetch('/admin/chat/templates/quick', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    this.templates = data.templates;
                    this.renderQuickResponses(this.templates);
                }
            }
        } catch (error) {
            console.error('Failed to load templates:', error);
        }
    }
    
    async loadTemplatesByType(type) {
        this.showLoading();
        this.updateActiveCategory(type);
        
        try {
            const response = await fetch(`/admin/chat/templates/by-type?type=${type}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    this.currentType = type;
                    this.renderQuickResponses(data.templates);
                }
            } else {
                throw new Error('Failed to load templates');
            }
        } catch (error) {
            console.error('Failed to load templates by type:', error);
            this.renderQuickResponses([]); // Show empty state
        } finally {
            this.hideLoading();
        }
    }
    
    async searchTemplates(query) {
        if (!query || query.length < 2) {
            this.hideSearchResults();
            return;
        }
        
        try {
            const response = await fetch(`/admin/chat/templates/search?query=${encodeURIComponent(query)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    this.renderSearchResults(data.templates);
                }
            }
        } catch (error) {
            console.error('Template search failed:', error);
        }
    }
    
    renderQuickResponses(templates) {
        const container = document.getElementById('quick-responses-list');
        if (!container) return;
        
        if (templates.length === 0) {
            container.innerHTML = `
                <div class="text-xs text-gray-500 dark:text-gray-400 italic py-2">
                    No ${this.currentType.replace('_', ' ')} templates found. 
                    <a href="/admin/chat/templates" class="text-blue-600 hover:underline">Create templates</a>
                </div>
            `;
            return;
        }
        
        const buttonsHTML = templates.map(template => `
            <button type="button" 
                    onclick="insertTemplate(${template.id}, '${this.escapeQuotes(template.message)}')"
                    class="quick-response-btn"
                    title="${this.escapeQuotes(template.message)}"
                    data-template-id="${template.id}">
                ${this.escapeHtml(template.name)}
                ${template.usage_count > 0 ? `<span class="ml-1 text-xs opacity-60">(${template.usage_count})</span>` : ''}
            </button>
        `).join('');
        
        container.innerHTML = buttonsHTML;
    }
    
    renderSearchResults(templates) {
        const container = document.getElementById('template-search-results');
        if (!container) return;
        
        if (templates.length === 0) {
            container.innerHTML = `
                <div class="template-search-item text-gray-500 dark:text-gray-400">
                    No templates found
                </div>
            `;
        } else {
            const resultsHTML = templates.map(template => `
                <div class="template-search-item" 
                     onclick="insertTemplate(${template.id}, '${this.escapeQuotes(template.message)}'); hideSearchResults();">
                    <div class="font-medium">${this.escapeHtml(template.name)}</div>
                    <div class="text-gray-500 dark:text-gray-400 truncate">
                        ${this.escapeHtml(template.message.substring(0, 60))}${template.message.length > 60 ? '...' : ''}
                    </div>
                </div>
            `).join('');
            
            container.innerHTML = resultsHTML;
        }
        
        container.classList.remove('hidden');
    }
    
    hideSearchResults() {
        const container = document.getElementById('template-search-results');
        if (container) {
            container.classList.add('hidden');
        }
    }
    
    updateActiveCategory(type) {
        document.querySelectorAll('.template-category-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.type === type) {
                btn.classList.add('active');
            }
        });
    }
    
    showLoading() {
        const loading = document.getElementById('templates-loading');
        if (loading) {
            loading.style.display = 'block';
        }
    }
    
    hideLoading() {
        const loading = document.getElementById('templates-loading');
        if (loading) {
            loading.style.display = 'none';
        }
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    escapeQuotes(text) {
        return text.replace(/'/g, "\\'").replace(/"/g, '\\"');
    }
}

// Enhanced template insertion with usage tracking
async function insertTemplate(templateId, message) {
    const textarea = document.getElementById('message-input');
    if (textarea) {
        textarea.value = message;
        textarea.focus();
        
        // Auto-resize textarea
        textarea.style.height = 'auto';
        textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
        
        // Trigger input event to enable send button
        textarea.dispatchEvent(new Event('input'));
        
        // Track template usage
        try {
            await fetch(`/admin/chat/templates/${templateId}/use`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
        } catch (error) {
            console.log('Template usage tracking failed:', error);
        }
    }
}

// Enhanced quick response function (backwards compatibility)
function insertQuickResponse(text) {
    const textarea = document.getElementById('message-input');
    if (textarea) {
        textarea.value = text;
        textarea.focus();
        textarea.style.height = 'auto';
        textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
        textarea.dispatchEvent(new Event('input'));
    }
}

// Global function for category buttons
function loadTemplatesByType(type) {
    if (window.chatTemplatesHandler) {
        window.chatTemplatesHandler.loadTemplatesByType(type);
    }
}

function hideSearchResults() {
    if (window.chatTemplatesHandler) {
        window.chatTemplatesHandler.hideSearchResults();
    }
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('quick-responses-container')) {
        window.chatTemplatesHandler = new ChatTemplatesHandler();
    }
});
</script>
@endpush
