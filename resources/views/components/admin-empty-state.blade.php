@props([
    'icon' => 'inbox',
    'title',
    'description' => null,
    'actionHref' => null,
    'actionLabel' => null,
    'compact' => false,
])

<div {{ $attributes->class(['admin-empty-state', 'admin-empty-state-compact' => $compact]) }}>
    <div class="admin-empty-state-icon" aria-hidden="true">
        @switch($icon)
            @case('announcement')
                <svg viewBox="0 0 24 24"><path d="M4 13.5V8.8a2 2 0 0 1 2-2h3.2L17 3.5v15l-7.8-3.3H6a2 2 0 0 1-2-1.7Z"></path><path d="M9.2 15.2 10.5 21H7.2L6 15.2"></path><path d="M19.5 8.2a5.2 5.2 0 0 1 0 5.1"></path></svg>
                @break
            @case('moderation')
                <svg viewBox="0 0 24 24"><path d="M12 21a9 9 0 1 0-9-9 9 9 0 0 0 9 9Z"></path><path d="m8 12 2.6 2.6L16.5 9"></path></svg>
                @break
            @case('concern')
                <svg viewBox="0 0 24 24"><path d="M5 18.5A3.5 3.5 0 0 1 1.5 15V7.5A3.5 3.5 0 0 1 5 4h14a3.5 3.5 0 0 1 3.5 3.5V15a3.5 3.5 0 0 1-3.5 3.5h-7L7 22v-3.5Z"></path><path d="M7 9h10M7 13h6"></path></svg>
                @break
            @case('archive')
                <svg viewBox="0 0 24 24"><path d="M4 7h16v13H4z"></path><path d="M3 4h18v3H3zM9 11h6"></path></svg>
                @break
            @default
                <svg viewBox="0 0 24 24"><path d="M4 5.5h16v13H4z"></path><path d="M4 14h4l2 2h4l2-2h4"></path><path d="M8 9h8"></path></svg>
        @endswitch
    </div>
    <h3>{{ $title }}</h3>
    @if($description)<p>{{ $description }}</p>@endif
    @if($actionHref && $actionLabel)<a class="admin-empty-state-action" href="{{ $actionHref }}">{{ $actionLabel }}</a>@endif
</div>
