<form method="post" action="{{ route('chats.messages.store', $chat) }}" class="mt-6 space-y-6">
    @csrf

    <div>
        <x-input-label for="content" :value="__('Message')" />
        <x-textarea-input id="content" name="content" class="mt-1 block w-full" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('content')" />
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>{{ __('Save') }}</x-primary-button>
    </div>
</form>
