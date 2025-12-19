<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Agenda Pediátrica') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <livewire:calendar />

                </div>
            </div>
        </div>
    </div>

    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        {{ __('Calendario') }}
    </x-nav-link>
    
    <x-nav-link :href="route('admin.settings')" :active="request()->routeIs('admin.settings')">
        {{ __('Mis Horarios') }}
    </x-nav-link>
</div
    </x-app-layout>