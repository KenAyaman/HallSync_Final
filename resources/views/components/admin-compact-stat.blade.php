@props([
    'icon' => 'summary',
    'value',
    'label',
    'note' => null,
    'tone' => 'gold',
])

<article {{ $attributes->class(['admin-compact-stat', 'admin-compact-stat-' . $tone]) }}>
    <div class="admin-compact-stat-icon" aria-hidden="true">
        @switch($icon)
            @case('calendar')
                <svg viewBox="0 0 24 24"><path d="M5 5h14v14H5zM8 3v4m8-4v4M5 9h14"/></svg>
                @break
            @case('clock')
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="7"/><path d="M12 8v4l3 2"/></svg>
                @break
            @case('check')
                <svg viewBox="0 0 24 24"><path d="m6 12 4 4 8-8"/></svg>
                @break
            @case('users')
                <svg viewBox="0 0 24 24"><path d="M16 18a4 4 0 0 0-8 0m4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm6 5a3 3 0 0 0-3-3m-6 0a3 3 0 0 0-3 3"/></svg>
                @break
            @case('user')
                <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3"/><path d="M6 19a6 6 0 0 1 12 0"/></svg>
                @break
            @case('shield')
                <svg viewBox="0 0 24 24"><path d="M12 3 5 6v5c0 4.5 3 8 7 10 4-2 7-5.5 7-10V6l-7-3Z"/><path d="M12 8v4m0 3h.01"/></svg>
                @break
            @case('alert')
                <svg viewBox="0 0 24 24"><path d="M12 4 4 19h16L12 4Z"/><path d="M12 9v4m0 3h.01"/></svg>
                @break
            @case('search')
                <svg viewBox="0 0 24 24"><circle cx="10.5" cy="10.5" r="5.5"/><path d="m15 15 4 4"/></svg>
                @break
            @case('archive')
                <svg viewBox="0 0 24 24"><path d="M5 7h14v12H5zM4 4h16v3H4zm5 7h6"/></svg>
                @break
            @case('building')
                <svg viewBox="0 0 24 24"><path d="M6 20V5h12v15M9 8h2m2 0h2m-6 4h2m2 0h2m-6 4h6"/></svg>
                @break
            @case('inbox')
                <svg viewBox="0 0 24 24"><path d="M5 5h14v14H5zM8 9h8m-8 4h5"/></svg>
                @break
            @default
                <svg viewBox="0 0 24 24"><path d="M5 5h14v14H5zM8 9h8m-8 4h8"/></svg>
        @endswitch
    </div>
    <div class="admin-compact-stat-main">
        <strong>{{ $value }}</strong>
        <span>{{ $label }}</span>
    </div>
    @if($note)
        <small>{{ $note }}</small>
    @endif
</article>
