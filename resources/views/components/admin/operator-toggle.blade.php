@props(['isOnline' => false])

<button
    id="toggle-operator-btn"
    data-initial-status="{{ $isOnline ? 'online' : 'offline' }}"
    type="button"
    class="px-3 py-2 text-sm rounded-md text-white flex items-center justify-center transition-all duration-200"
    :class="{
        'bg-green-500 animate-pulse': isOnline,
        'bg-red-500 animate-pulse': !isOnline
    }"
>
    <span id="btn-label">
        {{ $isOnline ? 'ONLINE' : 'OFFLINE' }}
    </span>
</button>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleBtn = document.getElementById('toggle-operator-btn');
        const label = toggleBtn.querySelector('#btn-label');

        let isOnline = toggleBtn.dataset.initialStatus === 'online';

        toggleBtn.addEventListener('click', async () => {
            const action = isOnline ? 'offline' : 'online';
            const url = `{{ route('admin.chat.operator.offline') }}`.replace('offline', action);

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    isOnline = data.status === 'online';
                    label.textContent = isOnline ? 'Go Offline' : 'Go Online';
                    toggleBtn.classList.toggle('bg-green-500', isOnline);
                    toggleBtn.classList.toggle('animate-pulse', isOnline);
                    toggleBtn.classList.toggle('bg-red-400', !isOnline);
                    toggleBtn.classList.toggle('animate-pulse', !isOnline);


                    showNotification(
                        isOnline ? 'You are now online for chat support' : 'You are now offline',
                        'success'
                    );
                } else {
                    showNotification('Failed to update status', 'error');
                }
            } catch (error) {
                console.error('Status toggle failed:', error);
                showNotification('Error updating operator status', 'error');
            }
        });

        async function loadOperatorStatus() {
            try {
                const response = await fetch('/admin/chat/operator/status');
                const data = await response.json();

                if (data.success) {
                    isOnline = data.is_online;
                    updateUI();
                }
            } catch (error) {
                console.error('Failed to load operator status:', error);
            }
        }

        function updateUI() {
            label.textContent = isOnline ? 'Go Offline' : 'Go Online';
            toggleBtn.classList.toggle('bg-green-500', isOnline);
            toggleBtn.classList.toggle('animate-pulse', isOnline);
            toggleBtn.classList.toggle('bg-gray-400', !isOnline);
        }

        function showNotification(message, type = 'success') {
            const existing = document.querySelector('.chat-notification');
            if (existing) existing.remove();

            const div = document.createElement('div');
            div.className = `chat-notification fixed top-4 right-4 px-4 py-2 rounded-md text-white z-50 transition-all duration-300 ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            }`;
            div.textContent = message;
            document.body.appendChild(div);

            setTimeout(() => {
                div.style.opacity = '0';
                div.style.transform = 'translateX(100%)';
                setTimeout(() => div.remove(), 300);
            }, 3000);
        }

        loadOperatorStatus();
    });
</script>
@endpush
