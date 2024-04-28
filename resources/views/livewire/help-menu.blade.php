<div @class([
    'hidden' => ! $documentation,
])
>
{{--    @teleport('.fi-kb-placeholder')--}}
        @if($documentation)
            <x-filament-actions::group
                :actions="$this->actions()"
                icon="heroicon-o-question-mark-circle"
                label="Nápověda"
                :button="true"
                icon-size="lg"
                color="gray"
            />
        @endif
{{--    @endteleport--}}

    <x-filament-actions::modals />
</div>
