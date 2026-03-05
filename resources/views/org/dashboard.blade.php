<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Organization Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-700">
                    আপনি <span class="font-semibold">ORG_ADMIN</span> রোলে লগইন করেছেন।
                    এই পেজে শুধুমাত্র Org Admin ঢুকতে পারে (role middleware enforced)।
                </div>
            </div>
        </div>
    </div>
</x-app-layout>