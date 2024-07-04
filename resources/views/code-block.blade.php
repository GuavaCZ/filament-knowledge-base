@pushonce('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono&display=swap');

        .shiki * {
            font-family: "JetBrains Mono", monospace;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
        }
    </style>
@endpushonce

<div @class([
    "bg-gray-900 text-gray-200 dark:bg-gray-900 rounded-lg p-2 pt-0 mb-4 ring-1 ring-gray-950/5 dark:ring-white/10",
])
     x-data
>
    <div class="text-xs text-white/50 py-2 px-1 flex flex-row items-center justify-between">
        <span>{{$language}}</span>
        <span
            class="hover:cursor-pointer hover:text-white/70 flex items-center justify-center gap-1 text-xs"
            x-on:click.prevent="
        if (navigator.clipboard) {
        navigator.clipboard.writeText($root.querySelector('.shiki').textContent);
        new FilamentNotification().title(filamentKnowledgeBaseTranslations.codeCopied).success().send();
        }
        const range = document.createRange();
        range.selectNodeContents($root.querySelector('.shiki'));
        window.getSelection().removeAllRanges();
        window.getSelection().addRange(range);
        ">
            <x-icon name='heroicon-o-clipboard' class='w-4 h-4'/>
            <span x-show="navigator.clipboard">Copy</span>
            <span x-show="! navigator.clipboard">Select</span>
        </span>
    </div>
    <div class="rounded-lg p-4 leading-relaxed bg-gray-700/80 [&_.shiki]:!bg-transparent">
        {!! $code !!}
    </div>
</div>
