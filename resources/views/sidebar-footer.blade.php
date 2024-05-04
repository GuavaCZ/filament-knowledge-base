<ul class="px-6 fi-sidebar-nav-groups mb-4 -mx-2 flex flex-col gap-y-7">
    <x-filament-panels::sidebar.item
        :active="$active"
        icon="heroicon-o-book-open"
        class="ring-1 ring-gray-950/10 dark:ring-white/20 rounded-lg"
        :url="$url">
        <x-filament::button
            class="!font-medium !text-gray-700 dark:!text-gray-200"
            style="box-shadow: none; background: none; padding:0;"
            color="gray"
            icon-size="lg"
            tag="span"
        >
            @lang('filament-knowledge-base::translations.knowledge-base')
        </x-filament::button>
    </x-filament-panels::sidebar.item>
</ul>
