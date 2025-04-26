<ul class="px-6 fi-sidebar-nav-groups mb-4 -mx-2 flex flex-col gap-y-7">
    <x-filament-panels::sidebar.item
        :icon="$icon"
        class="ring-1 ring-gray-950/10 dark:ring-white/20 rounded-lg"
        :url="$url"
        :should-open-url-in-new-tab="$shouldOpenUrlInNewTab">
        <x-filament::button
            class="!font-medium !text-gray-700 dark:!text-gray-200 hover:!bg-gray-100 focus-visible:!bg-gray-100 dark:hover:!bg-white/5 dark:focus-visible:!bg-white/5"
            style="box-shadow: none; background: none; padding:0;"
            color="gray"
            icon-size="lg"
            tag="span"
        >
            {{ $label }}
        </x-filament::button>
    </x-filament-panels::sidebar.item>
</ul>
