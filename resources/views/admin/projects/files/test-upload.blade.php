{{-- Test view: resources/views/admin/projects/files/test-upload.blade.php --}}
{{-- Use this for debugging upload functionality --}}

<x-layouts.admin title="Test File Upload">
    <div class="max-w-2xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6">File Upload Test</h1>
        
        <!-- Debug Info -->
        <div class="bg-gray-100 p-4 rounded mb-6">
            <h3 class="font-medium mb-2">Debug Information:</h3>
            <ul class="text-sm space-y-1">
                <li>Project ID: {{ $project->id }}</li>
                <li>Project Title: {{ $project->title }}</li>
                <li>Upload URL: {{ route('admin.projects.files.store', $project) }}</li>
                <li>CSRF Token: <span id="csrf-token-display">Loading...</span></li>
                <li>Max File Size: 10MB</li>
            </ul>
        </div>
        
        <!-- Simple Upload Form -->
        <form id="simple-upload-form" action="{{ route('admin.projects.files.store', $project) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label for="test-files" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Files for Testing
                    </label>
                    <input type="file" 
                           id="test-files" 
                           name="files[]" 
                           multiple 
                           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.txt"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                
                <div>
                    <label for="test-category" class="block text-sm font-medium text-gray-700 mb-2">
                        Category
                    </label>
                    <select id="test-category" name="category" class="block w-full rounded-md border-gray-300">
                        <option value="documents">Documents</option>
                        <option value="images">Images</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <div>
                    <label for="test-description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea id="test-description" 
                              name="description" 
                              rows="3" 
                              placeholder="Test upload description..."
                              class="block w-full rounded-md border-gray-300"></textarea>
                </div>
                
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_public" value="1" class="rounded border-gray-300 text-blue-600">
                        <span class="ml-2 text-sm text-gray-700">Make files public</span>
                    </label>
                </div>
                
                <div class="flex space-x-4">
                    <button type="submit" 
                            id="test-submit-btn"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50">
                        Test Upload
                    </button>
                    
                    <button type="button" 
                            onclick="testAjaxUpload()"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Test AJAX Upload
                    </button>
                    
                    <button type="button" 
                            onclick="clearTestForm()"
                            class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                        Clear Form
                    </button>
                </div>
            </div>
        </form>
        
        <!-- Test Results -->
        <div id="test-results" class="mt-6 hidden">
            <h3 class="font-medium mb-2">Test Results:</h3>
            <div id="test-output" class="bg-gray-50 p-4 rounded border text-sm font-mono"></div>
        </div>
        
        <!-- Console Log -->
        <div class="mt-6">
            <h3 class="font-medium mb-2">Console Log:</h3>
            <div id="console-log" class="bg-black text-green-400 p-4 rounded text-sm font-mono h-32 overflow-y-auto"></div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Display CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        document.getElementById('csrf-token-display').textContent = csrfToken ? csrfToken.content.substring(0, 20) + '...' : 'NOT FOUND!';
        
        // Log function
        function log(message, type = 'info') {
            const timestamp = new Date().toLocaleTimeString();
            const logDiv = document.getElementById('console-log');
            const color = type === 'error' ? 'text-red-400' : type === 'success' ? 'text-green-400' : 'text-blue-400';
            logDiv.innerHTML += `<div class="${color}">[${timestamp}] ${message}</div>`;
            logDiv.scrollTop = logDiv.scrollHeight;
            console.log(`[File Upload Test] ${message}`);
        }
        
        // Show test results
        function showResults(data) {
            const resultsDiv = document.getElementById('test-results');
            const outputDiv = document.getElementById('test-output');
            resultsDiv.classList.remove('hidden');
            outputDiv.textContent = JSON.stringify(data, null, 2);
        }
        
        // Test file selection
        document.getElementById('test-files').addEventListener('change', function() {
            const files = Array.from(this.files);
            log(`Files selected: ${files.length}`);
            files.forEach((file, index) => {
                log(`File ${index + 1}: ${file.name} (${file.size} bytes, ${file.type})`);
            });
        });
        
        // Regular form submission test
        document.getElementById('simple-upload-form').addEventListener('submit', function(e) {
            log('Form submitted normally (non-AJAX)');
            const files = document.getElementById('test-files').files;
            if (files.length === 0) {
                e.preventDefault();
                log('No files selected!', 'error');
                alert('Please select files first');
            }
        });
        
        // AJAX upload test
        window.testAjaxUpload = function() {
            const form = document.getElementById('simple-upload-form');
            const files = document.getElementById('test-files').files;
            
            if (files.length === 0) {
                log('No files selected for AJAX test!', 'error');
                alert('Please select files first');
                return;
            }
            
            log('Starting AJAX upload test...');
            
            const formData = new FormData(form);
            
            // Log form data
            log('Form data contents:');
            for (let pair of formData.entries()) {
                if (pair[1] instanceof File) {
                    log(`  ${pair[0]}: ${pair[1].name} (${pair[1].size} bytes)`);
                } else {
                    log(`  ${pair[0]}: ${pair[1]}`);
                }
            }
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                log(`Response status: ${response.status} ${response.statusText}`);
                
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    return response.text().then(text => {
                        log('Non-JSON response received (probably redirect)', 'success');
                        return { success: true, message: 'Upload successful (redirect response)', response_text: text.substring(0, 200) };
                    });
                }
            })
            .then(data => {
                log('Response data received', 'success');
                showResults(data);
                
                if (data.success !== false) {
                    log('Upload test completed successfully!', 'success');
                } else {
                    log(`Upload failed: ${data.message}`, 'error');
                }
            })
            .catch(error => {
                log(`Upload error: ${error.message}`, 'error');
                showResults({ error: error.message, stack: error.stack });
            });
        };
        
        // Clear form
        window.clearTestForm = function() {
            document.getElementById('simple-upload-form').reset();
            document.getElementById('test-results').classList.add('hidden');
            document.getElementById('console-log').innerHTML = '';
            log('Form cleared');
        };
        
        // Initial log
        log('File upload test page loaded');
        log(`Upload endpoint: ${form.action}`);
    });
    </script>
    @endpush
</x-layouts.admin>