<div id="@domid($message)" class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg space-y-6 [&>a]:first-of-type:[&_li]:rounded-t-lg [&>button]:first-of-type:[&_li]:rounded-t-lg [&>a]:last-of-type:[&_li]:rounded-b-lg [&>button]:last-of-type:[&_li]:rounded-b-lg">
    <p class="dark:text-gray-300">
        {{ $message->entryable->content }}

        @unless ($message->isComplete())
        <span class="animate-pulse">...</span>
        @endunless
    </p>
</div>
