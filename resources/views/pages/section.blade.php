@php
$sidebar = $this->getSubNavigationPosition();
@endphp
<x-filament-panels::page
    @class([
        "[&_.fi-page-sub-navigation-sidebar]:pl-4 [&_.fi-page-sub-navigation-sidebar]:border-l [&_.fi-page-sub-navigation-sidebar]:border-l-gray-600/10 [&_.fi-page-sub-navigation-sidebar]:dark:border-l-gray-600/30" => $sidebar === \Filament\Pages\SubNavigationPosition::End,
        "[&_.fi-page-sub-navigation-sidebar]:pr-4 [&_.fi-page-sub-navigation-sidebar]:border-r [&_.fi-page-sub-navigation-sidebar]:border-r-gray-600/10 [&_.fi-page-sub-navigation-sidebar]:dark:border-r-gray-600/30" => $sidebar === \Filament\Pages\SubNavigationPosition::Start,
    ])
:full-height="true"
>
    <div>
    {!!  $this->html !!}
    </div>

</x-filament-panels::page>
