@php
$sidebar = $this->getSubNavigationPosition();
@endphp

@push('scripts')
    <script>
        document.querySelectorAll('.gu-kb-anchor')
            .forEach((element) => element.addEventListener('click', function (event) {
                window.Livewire.dispatch('documentation.anchor.copy', {
                    url: event.target.href
                });
        }))
    </script>
@endpush
{{--<x-filament-panels::sidebar.item activ--}}
<x-filament-panels::page
    @class([
        "[&_.fi-sidebar-group]:sticky [&_.fi-sidebar-group]:top-20",
        "[&_.fi-page-sub-navigation-sidebar]:pl-4 [&_.fi-page-sub-navigation-sidebar]:ml-4 [&_.fi-page-sub-navigation-sidebar]:border-l [&_.fi-page-sub-navigation-sidebar]:border-l-gray-600/10 [&_.fi-page-sub-navigation-sidebar]:dark:border-l-gray-600/30" => $sidebar === \Filament\Pages\SubNavigationPosition::End,
        "[&_.fi-page-sub-navigation-sidebar]:pr-4 [&_.fi-page-sub-navigation-sidebar]:mr-4 [&_.fi-page-sub-navigation-sidebar]:border-r [&_.fi-page-sub-navigation-sidebar]:border-r-gray-600/10 [&_.fi-page-sub-navigation-sidebar]:dark:border-r-gray-600/30" => $sidebar === \Filament\Pages\SubNavigationPosition::Start,
    ])
        x-data="{
            currentSection: null,
        }"
:full-height="true"
>
    <div x-data
    x-init="
let anchors = document.querySelectorAll('.gu-kb-anchor');

let options = {
    root: null,
    rootMargin: '-15% 0px -65% 0px',
    threshold: 0.1
};

let classes = [
'transition', 'duration-300', 'ease-out','text-primary-600', 'dark:text-primary-400', 'translate-x-1'
];

let callback = function(entries, observer) {
    entries.forEach(entry => {
        if(entry.isIntersecting) {
          let section = '#' + entry.target.id;
          document.querySelectorAll('.fi-sidebar-item-button .fi-sidebar-item-label')
          .forEach((el) => el.classList.remove(...classes));
          let el = document.querySelector('.fi-sidebar-item-button[href=\'' + section +  '\'] .fi-sidebar-item-label');
          el.classList.add(...classes);
    }
    });
    }

    let observer = new IntersectionObserver(callback, options);

    anchors.forEach(anchor => observer.observe(anchor));
    "
    >
    {!!  $this->record->getHtml() !!}
    </div>


</x-filament-panels::page>
