<x-app-layout>
    <x-slot name="head">
        <x-turbo-refreshes-with method="morph" scroll="preserve" />
    </x-slot>

    <x-slot name="header">
        <h2 class="flex items-center space-x-1 font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <x-breadcrumbs :links="[route('dashboard') => __('Dashboard'), $chat->name]" />
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div id="@domid($chat, 'messages')" class="space-y-2">
                @foreach ($chat->messages as $message)
                    @include('messages.partials.message', ['message' => $message])
                @endforeach
            </div>

            <x-turbo-frame
                data-turbo-permanent
                :id="[$chat, 'create_message']"
                :src="route('chats.messages.create', $chat)"
                target="_top"
                data-controller="streams-turbo-streams"
                data-action="
                    turbo:before-fetch-request->streams-turbo-streams#prepareRequest
                    turbo:before-fetch-response->streams-turbo-streams#inspectFetchResponse
                "
            >
                <a href="{{ route('chats.messages.create', $chat)}}" class="dark:text-gray-200">New Message</a>
            </x-turbo-frame>
        </div>
    </div>
</x-app-layout>
