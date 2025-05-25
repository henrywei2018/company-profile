<x-layouts.admin title="Company Profile Overview">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Company Profile</h1>
            <p class="text-sm text-gray-600 dark:text-neutral-400">Overview of your company's identity, brand, and digital footprint</p>
        </div>
        <div class="flex items-center gap-3">
            <x-admin.button href="{{ route('admin.company-profile.edit') }}" color="primary"
                icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />'>
                Edit Profile
            </x-admin.button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <x-admin.stat-card title="Company Name" :value="$companyProfile->name"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7l6 6-6 6" />'
            iconColor="text-indigo-500" iconBg="bg-indigo-100 dark:bg-indigo-800/30" />

        <x-admin.stat-card title="Established" :value="$companyProfile->established"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m-4 4h4m-4 4h4m-4-4h4" />'
            iconColor="text-green-500" iconBg="bg-green-100 dark:bg-green-800/30" />

        <x-admin.stat-card title="Reg. Number" :value="$companyProfile->registration_number"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />'
            iconColor="text-amber-500" iconBg="bg-amber-100 dark:bg-amber-800/30" />
    </div>

    <!-- Detailed Information -->
    <x-admin.card>
        <x-slot name="header">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Company Information</h3>
        </x-slot>

        <div class="space-y-4">
            <div>
                <h4 class="font-semibold text-gray-800 dark:text-white">About</h4>
                <p class="text-sm text-gray-600 dark:text-neutral-400">{{ $companyProfile->about }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h5 class="text-sm font-medium text-gray-700 dark:text-neutral-300">Email</h5>
                    <p class="text-sm text-gray-600 dark:text-neutral-400">{{ $companyProfile->email }}</p>
                </div>
                <div>
                    <h5 class="text-sm font-medium text-gray-700 dark:text-neutral-300">Phone</h5>
                    <p class="text-sm text-gray-600 dark:text-neutral-400">{{ $companyProfile->phone }}</p>
                </div>
                <div>
                    <h5 class="text-sm font-medium text-gray-700 dark:text-neutral-300">Address</h5>
                    <p class="text-sm text-gray-600 dark:text-neutral-400">{{ $companyProfile->address }}</p>
                </div>
                <div>
                    <h5 class="text-sm font-medium text-gray-700 dark:text-neutral-300">Website</h5>
                    <p class="text-sm text-blue-600 dark:text-blue-400">
                        <a href="{{ $companyProfile->website }}" target="_blank">{{ $companyProfile->website }}</a>
                    </p>
                </div>
            </div>
        </div>
    </x-admin.card>
</x-layouts.admin>
