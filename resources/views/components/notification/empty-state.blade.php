{{-- resources/views/components/admin/notification/empty-state.blade.php --}}
@props([
    'variant' => 'admin'
])

<div class="px-4 py-8 text-center">
    <div class="flex flex-col items-center justify-center space-y-3">
        <!-- Empty State Icon -->
        <div class="relative">
            <svg class="mx-auto size-16 text-gray-400 dark:text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-5 5v-5zM9 7h6m-6 4h6m-6 4h6M3 7h3m-3 4h3m-3 4h3" />
            </svg>
            <!-- Decorative elements -->
            <div class="absolute -top-1 -right-1 size-4 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                <svg class="size-2.5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>

        <!-- Main Message -->
        <div class="space-y-1">
            <p class="text-sm font-medium text-gray-900 dark:text-white">
                Tidak Ada Notifikasi Baru
            </p>
        </div>

        <!-- Helpful Actions -->
        <div class="pt-2 space-y-2">
            @if($variant === 'admin')
                <div class="text-xs text-gray-400 dark:text-neutral-500 space-y-1">
                    <p>Anda akan diberi tahu ketika:</p>
                    <ul class="text-left space-y-0.5 pl-3">
                        <li>• Kutipan baru telah dikirimkan</li>
                        <li>• Proyek perlu perhatian</li>
                        <li>• Pesan telah diterima</li>
                        <li>• Peringatan sistem muncul</li>
                    </ul>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-2 pt-3">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 rounded-md transition-colors">
                        <svg class="size-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2V5a2 2 0 012-2h14a2 2 0 012 2v2" />
                        </svg>
                        Lihat Dashboard
                    </a>
                    <button type="button" 
                            onclick="refreshNotifications('admin')"
                            class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-50 hover:bg-gray-100 dark:bg-neutral-700 dark:text-neutral-300 dark:hover:bg-neutral-600 rounded-md transition-colors">
                        <svg class="size-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Refresh
                    </button>
                </div>
            @else
                <div class="text-xs text-gray-400 dark:text-neutral-500 space-y-1">
                    <p>Anda akan mendapatkan notifikasi tentang:</p>
                    <ul class="text-left space-y-0.5 pl-3">
                    <li>• Pembaruan & penyelesaian proyek</li>
                    <li>• Persetujuan penawaran</li>
                    <li>• Balasan pesan</li>
                    <li>• Tenggat waktu penting</li>
                    </ul>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-2 pt-3">
                    <a href="{{ route('client.quotations.create') }}" 
                       class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 rounded-md transition-colors">
                        <svg class="size-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Minta Penawaran
                    </a>
                    <a href="{{ route('client.messages.create') }}" 
                       class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-green-600 bg-green-50 hover:bg-green-100 dark:bg-green-900/30 dark:text-green-400 dark:hover:bg-green-900/50 rounded-md transition-colors">
                        <svg class="size-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Kirim Pesan
                    </a>
                </div>
            @endif
        </div>

        <!-- Notification Preferences Link -->
        {{-- <div class="pt-2 border-t border-gray-100 dark:border-neutral-700 w-full">
            <a href="{{ $variant === 'admin' ? route('admin.settings.notifications') : route('client.notifications.preferences') }}" 
               class="text-xs text-gray-500 hover:text-gray-700 dark:text-neutral-400 dark:hover:text-neutral-300 transition-colors">
                <svg class="size-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Notification Preferences
            </a>
        </div> --}}
    </div>
</div>