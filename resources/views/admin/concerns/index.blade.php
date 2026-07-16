<x-app-layout>
<div class="admin-concern-page">
    <section class="admin-overview-hero">
        <div>
            <p class="admin-overview-hero__kicker">HallSync Admin</p>
            <h1 class="admin-overview-hero__title">Concern <span>Management</span></h1>
            <span class="admin-overview-hero__subtitle">Review private resident complaints and send clear replies from one simple queue.</span>
        </div>
    </section>

    <section class="admin-concern-stats admin-compact-stats admin-compact-stats-3">
        <x-admin-compact-stat icon="inbox" :value="$submittedCount" label="Awaiting Reply" note="New complaints" />
        <x-admin-compact-stat icon="check" :value="$concerns->total() - $submittedCount" label="Replied" note="Resident notified" tone="green" />
        <x-admin-compact-stat icon="alert" :value="$urgentCount" label="Urgent" note="Review first" tone="red" />
    </section>

    <section class="admin-concern-panel">
        <div class="admin-concern-panel-head">
            <div>
                <h2>Concern Queue</h2>
                <p>{{ $concerns->total() }} concern{{ $concerns->total() === 1 ? '' : 's' }} · Open a record to review details and reply.</p>
            </div>
        </div>

        {{-- Search & Filter --}}
        <form method="GET" action="{{ route('admin.concerns.index') }}" class="admin-concern-filters">
            <label class="admin-concern-filter-field admin-concern-filter-wide">
                <span class="sr-only">Search concerns</span>
                <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="Search by subject, resident, or concern ID…" class="admin-concern-filter-input">
            </label>
            
            {{-- Status Dropdown --}}
            <div class="admin-concern-priority-dropdown" data-priority-dropdown>
                <select name="status" class="admin-concern-filter-select-native" aria-hidden="true" tabindex="-1">
                    <option value="all"      @selected($filters['status'] === 'all')>All statuses</option>
                    <option value="awaiting" @selected($filters['status'] === 'awaiting')>Awaiting reply</option>
                    <option value="replied"  @selected($filters['status'] === 'replied')>Replied</option>
                    <option value="open"     @selected($filters['status'] === 'open')>Open</option>
                    <option value="closed"   @selected($filters['status'] === 'closed')>Closed / Rejected</option>
                </select>
                <button type="button"
                        class="admin-concern-operations-filter"
                        aria-haspopup="listbox"
                        aria-expanded="false"
                        data-priority-trigger>
                    <span data-priority-label>
                        @if($filters['status'] === 'all') All statuses
                        @elseif($filters['status'] === 'awaiting') Awaiting reply
                        @elseif($filters['status'] === 'replied') Replied
                        @elseif($filters['status'] === 'open') Open
                        @elseif($filters['status'] === 'closed') Closed / Rejected
                        @endif
                    </span>
                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="m6 9 6 6 6-6" />
                    </svg>
                </button>
                <div class="admin-concern-priority-menu" role="listbox" data-priority-menu hidden>
                    <button type="button" role="option" aria-selected="{{ $filters['status'] === 'all' ? 'true' : 'false' }}" data-priority-option value="all">All statuses</button>
                    <button type="button" role="option" aria-selected="{{ $filters['status'] === 'awaiting' ? 'true' : 'false' }}" data-priority-option value="awaiting">Awaiting reply</button>
                    <button type="button" role="option" aria-selected="{{ $filters['status'] === 'replied' ? 'true' : 'false' }}" data-priority-option value="replied">Replied</button>
                    <button type="button" role="option" aria-selected="{{ $filters['status'] === 'open' ? 'true' : 'false' }}" data-priority-option value="open">Open</button>
                    <button type="button" role="option" aria-selected="{{ $filters['status'] === 'closed' ? 'true' : 'false' }}" data-priority-option value="closed">Closed / Rejected</button>
                </div>
            </div>
            
            {{-- Category Dropdown --}}
            <div class="admin-concern-priority-dropdown" data-priority-dropdown>
                <select name="category" class="admin-concern-filter-select-native" aria-hidden="true" tabindex="-1">
                    <option value="all" @selected($filters['category'] === 'all')>All categories</option>
                    @foreach(\App\Models\Concern::CATEGORIES as $key => $label)
                        <option value="{{ $key }}" @selected($filters['category'] === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="button"
                        class="admin-concern-operations-filter"
                        aria-haspopup="listbox"
                        aria-expanded="false"
                        data-priority-trigger>
                    <span data-priority-label>
                        @if($filters['category'] === 'all') All categories
                        @else
                            <?php 
                            $selectedCategoryLabel = \App\Models\Concern::CATEGORIES[$filters['category']] ?? 'All categories';
                            echo $selectedCategoryLabel;
                            ?>
                        @endif
                    </span>
                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="m6 9 6 6 6-6" />
                    </svg>
                </button>
                <div class="admin-concern-priority-menu" role="listbox" data-priority-menu hidden>
                    <button type="button" role="option" aria-selected="{{ $filters['category'] === 'all' ? 'true' : 'false' }}" data-priority-option value="all">All categories</button>
                    @foreach(\App\Models\Concern::CATEGORIES as $key => $label)
                        <button type="button" role="option" aria-selected="{{ $filters['category'] === $key ? 'true' : 'false' }}" data-priority-option value="{{ $key }}">{{ $label }}</button>
                    @endforeach
                </div>
            </div>
            
            <button type="submit" class="admin-concern-filter-btn">Apply</button>
            @if($filters['search'] || $filters['status'] !== 'all' || $filters['category'] !== 'all')
                <a href="{{ route('admin.concerns.index') }}" class="admin-concern-filter-clear">Clear</a>
            @endif
        </form>

        @php
            $awaitingConcerns = $concerns->filter(fn($c) => !$c->admin_reply);
            $repliedConcerns  = $concerns->filter(fn($c) => $c->admin_reply);
            $showAll = $filters['status'] === 'all' && !$filters['search'] && $filters['category'] === 'all';
        @endphp

        @if($concerns->isEmpty())
            <div style="padding: 14px 18px;">
                <x-admin-empty-state icon="concern" title="No concerns found" description="{{ $filters['search'] ? 'No results match your search. Try adjusting the filters.' : 'Resident complaints will appear here for review.' }}" />
            </div>
        @else
            @if($showAll && $awaitingConcerns->isNotEmpty())
                <div class="admin-concern-section-head">
                    <span class="admin-concern-section-label admin-concern-section-label-awaiting">Awaiting Reply</span>
                    <span class="admin-concern-section-count">{{ $awaitingConcerns->count() }} on this page</span>
                </div>
                <div class="admin-concern-list">
                    @foreach($awaitingConcerns as $concern)
                        <article class="admin-concern-row">
                            <div class="admin-concern-row-main">
                                <small>{{ $concern->concern_id }} &middot; {{ $concern->category_label }} &middot; {{ $concern->is_anonymous ? 'Anonymous Resident' : ($concern->user->name ?? 'Unknown') }} &middot; {{ $concern->created_at->format('M d, Y') }}</small>
                                <h3>{{ $concern->subject }}</h3>
                                <p>{{ Str::limit($concern->details, 140) }}</p>
                            </div>
                            <div class="admin-concern-row-side">
                                <span class="admin-concern-status-badge admin-concern-status-awaiting">Awaiting Reply</span>
                                <a href="{{ route('admin.concerns.show', $concern) }}" class="admin-concern-row-action">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View &amp; Reply
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif

            @if($showAll && $repliedConcerns->isNotEmpty())
                <div class="admin-concern-section-head admin-concern-section-head-replied">
                    <span class="admin-concern-section-label admin-concern-section-label-replied">Replied</span>
                    <span class="admin-concern-section-count">{{ $repliedConcerns->count() }} on this page</span>
                </div>
                <div class="admin-concern-list">
                    @foreach($repliedConcerns as $concern)
                        <article class="admin-concern-row">
                            <div class="admin-concern-row-main">
                                <small>{{ $concern->concern_id }} &middot; {{ $concern->category_label }} &middot; {{ $concern->is_anonymous ? 'Anonymous Resident' : ($concern->user->name ?? 'Unknown') }} &middot; {{ $concern->created_at->format('M d, Y') }}</small>
                                <h3>{{ $concern->subject }}</h3>
                                <p>{{ Str::limit($concern->details, 140) }}</p>
                            </div>
                            <div class="admin-concern-row-side">
                                <span class="admin-concern-status-badge admin-concern-status-replied">Replied</span>
                                <a href="{{ route('admin.concerns.show', $concern) }}" class="admin-concern-row-action">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View Details
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif

            @if(!$showAll)
                <div class="admin-concern-list">
                    @foreach($concerns as $concern)
                        <article class="admin-concern-row">
                            <div class="admin-concern-row-main">
                                <small>{{ $concern->concern_id }} &middot; {{ $concern->category_label }} &middot; {{ $concern->is_anonymous ? 'Anonymous Resident' : ($concern->user->name ?? 'Unknown') }} &middot; {{ $concern->created_at->format('M d, Y') }}</small>
                                <h3>{{ $concern->subject }}</h3>
                                <p>{{ Str::limit($concern->details, 140) }}</p>
                            </div>
                            <div class="admin-concern-row-side">
                                <span class="admin-concern-status-badge admin-concern-status-{{ $concern->admin_reply ? 'replied' : 'awaiting' }}">
                                    {{ $concern->admin_reply ? 'Replied' : 'Awaiting Reply' }}
                                </span>
                                <a href="{{ route('admin.concerns.show', $concern) }}" class="admin-concern-row-action">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    {{ $concern->admin_reply ? 'View Details' : 'View &amp; Reply' }}
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        @endif

        @if($concerns->hasPages())
            <div class="admin-concern-pagination">{{ $concerns->links() }}</div>
        @endif
    </section>
</div>

<style>
.admin-concern-page {
    max-width: 1580px;
    margin: 0 auto;
    display: grid;
    gap: 18px
}
.admin-concern-hero, .admin-concern-panel {
    border: 1px solid #e3d8ca;
    border-radius: 14px;
    background: #f7f6f3
}
.admin-concern-hero {
    padding: 24px 26px
}
.admin-concern-kicker {
    margin: 0 0 7px;
    color: #c06f00;
    font-size: .68rem;
    font-weight: 800;
    letter-spacing: .17em;
    text-transform: uppercase
}
.admin-concern-title, .admin-concern-panel h2 {
    margin: 0;
    color: #342a23;
    font-family: 'Playfair Display', serif;
    font-weight: 400
}
.admin-concern-title {
    font-size: clamp(2.2rem, 4vw, 3.4rem)
}
.admin-concern-subtitle {
    display: block;
    margin-top: 8px;
    color: #786b60;
    font-size: .9rem;
    line-height: 1.6
}
.admin-concern-panel {
    overflow: hidden
}
.admin-concern-panel-head {
    padding: 16px 18px 13px;
    border-bottom: 1px solid #e3d8ca
}
.admin-concern-panel h2 {
    font-size: 1.55rem
}
.admin-concern-panel p {
    margin: 5px 0 0;
    color: #786b60;
    font-size: .82rem;
    line-height: 1.55
}
/* Search/filter bar */
.admin-concern-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    padding: 12px 18px;
    border-bottom: 1px solid #e3d8ca;
    align-items: center;
    background: #fbf8f3
}
.admin-concern-filter-field {
    display: flex;
    flex-direction: column
}
.admin-concern-filter-wide {
    flex: 1 1 240px
}
.admin-concern-filter-input, .admin-concern-filter-select {
    min-height: 34px;
    padding: 0 10px;
    border: 1px solid #dfd5c8;
    border-radius: 7px;
    background: #fffdf9;
    color: #453b33;
    font: inherit;
    font-size: .8rem;
    outline: none
}
.admin-concern-filter-input:focus, .admin-concern-filter-select:focus {
    border-color: #c6954a;
    box-shadow: 0 0 0 3px rgba(198, 149, 74, .12)
}
.admin-concern-filter-btn{
    /* --- Layout & Structure (From Main Button) --- */
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 45px;
    padding: 0 20px;
    border: none; /* Removed the harsh red/brown border to favor the gradient */
    border-radius: 777px;
    white-space: nowrap;

    /* --- Typography (From Main Button) --- */
    font-size: .74rem;
    font-weight: 800;
    letter-spacing: .075em;
    line-height: 1;
    text-transform: uppercase;
    text-decoration: none;

    /* --- Color & Depth (From "Better" Button) --- */
    background: linear-gradient(90deg, #B8842F 0%, #D6A85B 100%);
    color: #FFFFFF;
    box-shadow: 0 12px 28px rgba(199, 150, 69, 0.3);

    /* --- Smooth Transitions (From "Better" Button) --- */
    transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
}

/* --- The Combined Premium Hover State --- */
.admin-concern-filter-btn:hover,
.admin-concern-filter-btn:focus-visible {
    outline: none;
    /* Clean CSS alternative to the JS onmouseover */
    transform: translateY(-3px); 
    box-shadow: 0 20px 40px rgba(199, 150, 69, 0.4);
    
    /* Optional: If you want a slight color shifting glow on hover, 
       we can subtly shift the gradient look by brightening it slightly */
    filter: brightness(1.05); 
}

/* --- Clean Reset for Active (Click) State --- */
.admin-concern-filter-btn:active {
    transform: translateY(-1px);
    box-shadow: 0 8px 16px rgba(199, 150, 69, 0.3);
}

/* ==========================================================================
   CUSTOM DROPDOWN FOR CONCERN FILTERS (Match Operations Queue style)
   ========================================================================== */

.admin-concern-filter-select-native {
    position: absolute;
    width: 1px;
    height: 1px;
    overflow: hidden;
    opacity: 0;
    pointer-events: none;
}

/* 1. Interactive Wrapper Container */
.admin-concern-filters .admin-concern-priority-dropdown {
    position: relative;
    display: inline-flex;
    z-index: 40;
}

/* 2. Base Filter Button Styling - Without golden pill */
.admin-concern-filters .admin-concern-operations-filter {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 45px;
    padding: 0 20px;
    border: 1px solid #dfd5c8;
    border-radius: 777px;
    white-space: nowrap;
    font-size: .74rem;
    font-weight: 800;
    letter-spacing: .075em;
    line-height: 1;
    text-transform: uppercase;
    text-decoration: none;
    background: #fffdf9;
    color: #453b33;
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease, filter 0.3s ease, border-color 0.2s ease;
}

/* 3. Embedded Chevron Icon Configuration */
.admin-concern-filters .admin-concern-operations-filter svg {
    width: 16px;
    height: 16px;
    flex: 0 0 auto;
    fill: currentColor;
    pointer-events: none;
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

/* 4. Hover & Focus Micro-Animations */
.admin-concern-filters .admin-concern-operations-filter:hover,
.admin-concern-filters .admin-concern-operations-filter:focus-visible {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(199, 150, 69, 0.18);
    border-color: #c6954a;
    background: #fffdf9;
    color: #453b33;
    outline: none;
}

/* Subtle downward bounce on the inner chevron during button hover */
.admin-concern-filters .admin-concern-operations-filter:hover svg {
    transform: translateY(1px);
}

/* 5. Click Active / Pressed Physics */
.admin-concern-filters .admin-concern-operations-filter:active {
    transform: translateY(0);
    box-shadow: 0 2px 8px rgba(199, 150, 69, 0.12);
    border-color: #b8842f;
}

/* 6. Active State When Dropdown Menu Is Open */
.admin-concern-filters .admin-concern-priority-dropdown.is-open .admin-concern-operations-filter {
    transform: translateY(-1px);
    background: #fffdf9;
    color: #453b33;
    box-shadow: 0 4px 12px rgba(199, 150, 69, 0.18);
    border-color: #c6954a;
    filter: brightness(1);
}

/* Flipping the internal arrow completely upside down when open */
.admin-concern-filters .admin-concern-priority-dropdown.is-open .admin-concern-operations-filter svg {
    transform: rotate(180deg) !important;
}

/* 7. Menu Overlay */
.admin-concern-filters .admin-concern-priority-menu {
    position: absolute;
    top: calc(100% + 6px);
    right: 0;
    z-index: 50;
    width: 190px;
    overflow: hidden;
    padding: 6px;
    border: 1px solid rgba(107, 79, 58, 0.16);
    border-radius: 14px;
    background: #fffaf5;
    box-shadow: 0 16px 32px rgba(47, 39, 31, 0.18);
}

.admin-concern-filters .admin-concern-priority-menu[hidden] {
    display: none;
}

.admin-concern-filters .admin-concern-priority-menu button {
    display: flex;
    width: 100%;
    min-height: 36px;
    align-items: center;
    padding: 0 11px;
    border: 0;
    border-radius: 9px;
    background: #ffffff;
    color: #4d3b2e;
    font-size: 0.78rem;
    font-weight: 700;
    text-align: left;
    cursor: pointer;
    transition: background-color 0.16s ease, color 0.16s ease;
}

.admin-concern-filters .admin-concern-priority-menu button:hover,
.admin-concern-filters .admin-concern-priority-menu button:focus-visible {
    background: #fff2e8;
    color: #8f2929;
    outline: none;
}

.admin-concern-filters .admin-concern-priority-menu button[aria-selected="true"] {
    background: #f2dfd2;
    color: #7a4f16;
}

/* Section headers */
.admin-concern-section-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 12px 18px 10px;
    background: #fbf8f3;
    border-top: 2px solid #e3d8ca;
    border-bottom: 1px solid #e3d8ca
}
.admin-concern-section-head-replied {
    background: #f7f6f3
}
.admin-concern-section-label {
    font-size: .72rem;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: #342a23;
    cursor: default
}
.admin-concern-section-label-awaiting {
    color: #68400f
}
.admin-concern-section-label-replied {
    color: #356140
}
.admin-concern-section-count {
    font-size: .68rem;
    color: #9b8d81;
    font-weight: 500
}
/* Concern list */
.admin-concern-list {
    display: flex;
    flex-direction: column
}
.admin-concern-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 18px;
    padding: 16px 18px;
    border-bottom: 1px solid #e3d8ca
}
.admin-concern-row:last-child {
    border-bottom: none
}
.admin-concern-row:hover {
    background: #fbf8f3
}
.admin-concern-row-main {
    flex: 1;
    min-width: 0
}
.admin-concern-row small {
    color: #9b8d81;
    font-size: .68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em
}
.admin-concern-row h3 {
    margin: 6px 0 5px;
    color: #342a23;
    font-size: .96rem;
    font-weight: 600;
    line-height: 1.3
}
.admin-concern-row p {
    margin: 0;
    color: #786b60;
    font-size: .81rem;
    line-height: 1.55
}
.admin-concern-row-side {
    display: flex;
    flex-shrink: 0;
    flex-direction: row;
    align-items: center;
    justify-content: flex-end;
    gap: 10px
}
.admin-concern-status-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 28px;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: .68rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .06em;
    white-space: nowrap
}
.admin-concern-status-awaiting {
    border: 1px solid #d2ae7b;
    background: transparent;
    color: #68400f
}
.admin-concern-status-replied {
    border: 1px solid #9fc6a8;
    background: transparent;
    color: #356140
}
.admin-concern-row-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 38px;
    min-width: 130px;
    padding: .5rem 1rem;
    border: 1px solid rgba(214, 168, 91, .46);
    border-radius: 8px;
    background: rgba(214, 168, 91, .16);
    color: #7a4f16;
    font-size: .8125rem;
    font-weight: 700;
    line-height: 1.2;
    text-decoration: none;
    white-space: nowrap;
    transition: background .18s ease, border-color .18s ease, color .18s ease, transform .18s ease
}
.admin-concern-row-action:hover {
    border-color: rgba(214, 168, 91, .62);
    background: rgba(214, 168, 91, .24);
    color: #65400f;
    transform: translateY(-1px)
}
.admin-concern-row-action svg {
    display: block;
    width: 14px;
    height: 14px;
    flex: 0 0 14px
}
body.role-manager .admin-content-shell .admin-concern-page .admin-concern-row .admin-concern-row-action {
    min-height: 38px!important;
    min-width: 130px!important;
    padding: .5rem 1rem!important;
    border-color: rgba(214, 168, 91, .46)!important;
    background: rgba(214, 168, 91, .16)!important;
    color: #7a4f16!important
}
body.role-manager .admin-content-shell .admin-concern-page .admin-concern-row .admin-concern-row-action:hover {
    border-color: rgba(214, 168, 91, .62)!important;
    background: rgba(214, 168, 91, .24)!important;
    color: #65400f!important
}
.admin-concern-pagination {
    padding: 13px 18px;
    border-top: 1px solid #e3d8ca
}
@media(max-width:680px) {
    .admin-concern-row {
        flex-direction: column;
        align-items: flex-start
    }
    .admin-concern-row-side {
        align-items: flex-start;
        width: 100%;
        flex-direction: row;
        justify-content: space-between
    }
    .admin-concern-filters {
        flex-direction: column
    }
    .admin-concern-filter-wide {
        width: 100%
    }
}
</style>

<script>
// Concern page dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    const dropdowns = document.querySelectorAll('.admin-concern-priority-dropdown');
    
    dropdowns.forEach(dropdown => {
        const trigger = dropdown.querySelector('[data-priority-trigger]');
        const menu = dropdown.querySelector('[data-priority-menu]');
        const label = dropdown.querySelector('[data-priority-label]');
        const hiddenSelect = dropdown.querySelector('.admin-concern-filter-select-native');
        const options = dropdown.querySelectorAll('[data-priority-option]');
        
        if (!trigger || !menu || !label || !hiddenSelect) return;
        
        // Toggle dropdown
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const isOpen = dropdown.classList.contains('is-open');
            
            // Close all other dropdowns
            document.querySelectorAll('.admin-concern-priority-dropdown.is-open').forEach(d => {
                if (d !== dropdown) {
                    d.classList.remove('is-open');
                    d.querySelector('[data-priority-menu]').hidden = true;
                    d.querySelector('[aria-expanded]').setAttribute('aria-expanded', 'false');
                }
            });
            
            // Toggle current dropdown
            if (isOpen) {
                dropdown.classList.remove('is-open');
                menu.hidden = true;
                trigger.setAttribute('aria-expanded', 'false');
            } else {
                dropdown.classList.add('is-open');
                menu.hidden = false;
                trigger.setAttribute('aria-expanded', 'true');
            }
        });
        
        // Handle option selection
        options.forEach(option => {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const value = this.getAttribute('value');
                const text = this.textContent.trim();
                
                // Update the label
                label.textContent = text;
                
                // Update the hidden select value
                hiddenSelect.value = value;
                
                // Update aria-selected on all options
                options.forEach(opt => {
                    opt.setAttribute('aria-selected', opt.getAttribute('value') === value ? 'true' : 'false');
                });
                
                // Close the dropdown
                dropdown.classList.remove('is-open');
                menu.hidden = true;
                trigger.setAttribute('aria-expanded', 'false');
                
                // Submit the form
                trigger.closest('form').submit();
            });
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.admin-concern-priority-dropdown')) {
            document.querySelectorAll('.admin-concern-priority-dropdown.is-open').forEach(dropdown => {
                dropdown.classList.remove('is-open');
                dropdown.querySelector('[data-priority-menu]').hidden = true;
                dropdown.querySelector('[aria-expanded]').setAttribute('aria-expanded', 'false');
            });
        }
    });
});
</script>
</x-app-layout>
