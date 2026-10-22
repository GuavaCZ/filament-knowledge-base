<span id="gu-kb-help-menu-anchor" hidden></span>

{{-- Keep the teleport source outside Filament's persisted topbar so navigation remounts the menu. --}}
@push('scripts')
    <div x-data>
        <template x-teleport.prepend="#gu-kb-help-menu-anchor">
            @livewire('help-menu')
        </template>
    </div>
@endpush
