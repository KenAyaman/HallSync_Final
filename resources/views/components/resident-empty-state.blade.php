@props([
    'icon' => 'inbox',
    'title',
    'description' => null,
    'actionHref' => null,
    'actionLabel' => null,
    'compact' => false,
])

<div {{ $attributes->class(['resident-unified-empty', 'resident-unified-empty-compact' => $compact]) }}>
    <div class="resident-unified-empty-icon" aria-hidden="true">
        @switch($icon)
            @case('booking')
                <svg viewBox="0 0 24 24"><path d="M6 3v3M18 3v3M4 9h16"></path><rect x="3" y="5" width="18" height="16" rx="3"></rect><path d="m9 15 2 2 4-4"></path></svg>
                @break
            @case('community')
                <svg viewBox="0 0 24 24"><path d="M5 17.5A3.5 3.5 0 0 1 1.5 14V7.5A3.5 3.5 0 0 1 5 4h14a3.5 3.5 0 0 1 3.5 3.5V14a3.5 3.5 0 0 1-3.5 3.5h-7L7 21v-3.5Z"></path><path d="M7 9h10M7 13h6"></path></svg>
                @break
            @case('concern')
                <svg viewBox="0 0 24 24"><path d="M12 21a9 9 0 1 0-9-9 9 9 0 0 0 9 9Z"></path><path d="M12 7v6M12 16.5h.01"></path></svg>
                @break
            @case('announcement')
                <svg viewBox="0 0 24 24"><path d="M4 13.5V8.8a2 2 0 0 1 2-2h3.2L17 3.5v15l-7.8-3.3H6a2 2 0 0 1-2-1.7Z"></path><path d="M9.2 15.2 10.5 21H7.2L6 15.2M19.5 8.2a5.2 5.2 0 0 1 0 5.1"></path></svg>
                @break
            @case('archive')
                <svg viewBox="0 0 24 24"><path d="M4 7h16v13H4z"></path><path d="M3 4h18v3H3zM9 11h6"></path></svg>
                @break
            @default
                <svg viewBox="0 0 24 24"><path d="M4 5.5h16v13H4z"></path><path d="M4 14h4l2 2h4l2-2h4M8 9h8"></path></svg>
        @endswitch
    </div>
    <h3>{{ $title }}</h3>
    @if($description)<p>{{ $description }}</p>@endif
    @if($actionHref && $actionLabel)<a class="resident-unified-empty-action" href="{{ $actionHref }}">{{ $actionLabel }}</a>@endif
</div>
