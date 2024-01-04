<x-app-layout>
    <x-slot name="header">
        <h2 class="flex items-center space-x-1 font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <x-breadcrumbs :links="[route('dashboard') => __('Dashboard'), route('chats.show', $chat) => $chat->name, __('New Chat')]" />
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Chat Message Information') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __("Say something nice.") }}
                            </p>
                        </header>

                        <x-turbo::frame :id="[$chat, 'create_message']" target="_top">
                            @include('chat-messages.partials.message-form', ['chat' => $chat])
                        </x-turbo::frame>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
