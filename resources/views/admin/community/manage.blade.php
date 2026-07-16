<x-app-layout>
<div class="space-y-8 admin-shell admin-community-page">

    <section class="admin-overview-hero">
        <div>
            <p class="admin-overview-hero__kicker">HallSync Admin</p>
            <h1 class="admin-overview-hero__title">Resident <span>Community Hub</span></h1>
            <span class="admin-overview-hero__subtitle">Review resident submissions, approve quality content, and maintain community standards.</span>
        </div>
    </section>

    {{-- STATS CARDS --}}
    @php
        $pendingCount = $pendingPosts->total();
        $approvedCount = $approvedPosts->count();
        $rejectedCount = $rejectedPosts->count();
        $totalCount = $pendingCount + $approvedCount + $rejectedCount;
    @endphp

    <div class="admin-feature-stat-grid admin-compact-stats admin-compact-stats-4">
        <x-admin-compact-stat icon="archive" :value="$totalCount" label="Total Posts" note="All time submissions" />
        <x-admin-compact-stat icon="clock" :value="$pendingCount" label="Pending Review" note="Awaiting moderation" tone="blue" />
        <x-admin-compact-stat icon="check" :value="$approvedCount" label="Published" note="Approved content" tone="green" />
        <x-admin-compact-stat icon="archive" :value="$rejectedCount" label="Rejected" note="Declined submissions" tone="red" />
    </div>

    <div class="admin-ticket-panel admin-ticket-archive admin-community-archive">
        {{-- TABS FOR DIFFERENT STATUSES --}}
        <nav class="admin-archive-tabs admin-community-tabs" role="tablist" aria-label="Community post statuses">
            <button type="button" onclick="filterByStatus('pending', this)" class="filter-tab active admin-archive-tab admin-community-tab is-active" data-community-tab="assigned" role="tab" aria-selected="true">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7h3a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h3" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h6v4H9z" />
                </svg>
                Assigned <span>{{ $pendingCount }}</span>
            </button>
            <button type="button" onclick="filterByStatus('approved', this)" class="filter-tab admin-archive-tab admin-community-tab" data-community-tab="finished" role="tab" aria-selected="false">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 6 9 17l-5-5" />
                </svg>
                Finished <span>{{ $approvedCount }}</span>
            </button>
            <button type="button" onclick="filterByStatus('rejected', this)" class="filter-tab admin-archive-tab admin-community-tab" data-community-tab="rejected" role="tab" aria-selected="false">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 6 6 18M6 6l12 12" />
                </svg>
                Rejected <span>{{ $rejectedCount }}</span>
            </button>
        </nav>

        {{-- PENDING POSTS SECTION --}}
        <div id="pending-section" class="post-section">
            <div class="admin-community-review-panel" style="background: linear-gradient(180deg, #2A2C30 0%, #1F2023 100%); border-radius: 32px; padding: 28px 32px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.4); border: 1px solid #3A342D;">
                <div class="admin-brown-panel-head" style="display:flex; justify-content:flex-end; align-items:center; gap:16px; margin-bottom: 24px; background: #6B4F3A; padding: 22px 24px 16px;">
                    <div class="flex items-center gap-2">
                        <span class="admin-community-status admin-community-status-pending text-xs px-3 py-1 rounded-full" style="background: rgba(224,112,96,0.15); color: #E07060;">
                            {{ $pendingCount }} pending approval
                        </span>
                    </div>
                </div>

                <div style="height:1px; background: linear-gradient(to right, rgba(214,168,91,0.3), rgba(214,168,91,0.05), transparent); margin-bottom: 24px;"></div>

                <div data-progressive-list>
                @forelse($pendingPosts as $post)
                    <div class="post-item admin-community-review-card" data-progressive-item data-status="pending"
                         style="background: linear-gradient(135deg, #2C2C2F 0%, #25272A 100%); border: 1px solid rgba(58,52,45,0.6); border-radius: 24px; padding: 18px 20px; margin-bottom: 12px; transition: all 0.3s ease;"
                         onmouseover="this.style.transform='translateX(4px)'; this.style.borderColor='rgba(214,168,91,0.4)'; this.style.boxShadow='0 12px 32px rgba(0,0,0,0.4)';"
                         onmouseout="this.style.transform='translateX(0)'; this.style.borderColor='rgba(58,52,45,0.6)'; this.style.boxShadow='none';">

                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                            <div class="flex-1">
                                <small class="admin-community-clean-meta">
                                    POST #{{ $post->id }} &middot; {{ strtoupper(str_replace('_', ' ', $post->type)) }} &middot; {{ $post->created_at->format('M d, Y') }}
                                </small>
                                <h3 class="admin-community-clean-title">{{ $post->title }}</h3>
                                <p class="admin-community-clean-copy">{{ Str::limit($post->content, 100) }}</p>

                                <div class="admin-community-clean-hidden flex flex-wrap items-center gap-3 mb-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg" style="background: rgba(214,168,91,0.12);">
                                        @if($post->type === 'lost_found') 🔍
                                        @elseif($post->type === 'buy_sell') 💰
                                        @elseif($post->type === 'event') 🎉
                                        @else 💬
                                        @endif
                                    </div>
                                    <div>
                                        <h3 style="font-size: 17px; font-weight: 700; color: #F8F3EA; margin: 0;">{{ $post->title }}</h3>
                                        <div class="flex flex-wrap items-center gap-3 mt-2">
                                            <span class="text-xs px-2 py-1 rounded-full" style="background: rgba(214,168,91,0.15); color: #D6A85B;">
                                                {{ strtoupper(str_replace('_', ' ', $post->type)) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-2 flex-shrink-0 flex-wrap">
                                <a href="{{ route('community.show', $post) }}"
                                   class="admin-community-action admin-community-action-secondary px-4 py-2 rounded-lg text-xs font-bold flex items-center gap-2 transition-all duration-200 text-decoration-none"
                                   style="background: rgba(214,168,91,0.12); color: #B47721; border: 1px solid rgba(214,168,91,0.22); text-decoration: none;"
                                   onmouseover="this.style.background='rgba(214,168,91,0.2)'"
                                   onmouseout="this.style.background='rgba(214,168,91,0.12)'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View Details
                                </a>
                                <form method="POST" action="{{ route('community.approve', $post) }}">
                                    @csrf
                                    <button type="submit"
                                            class="admin-community-action admin-community-action-approve px-4 py-2 rounded-lg text-xs font-bold flex items-center gap-2 transition-all duration-200"
                                            style="background: linear-gradient(135deg, #5A8A5A, #6DA76D); color: white; border: none;"
                                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(90,138,90,0.4)'"
                                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Approve
                                    </button>
                                </form>
                                <button onclick="openRejectModal({{ $post->id }})" 
                                        class="admin-community-action admin-community-action-reject px-4 py-2 rounded-lg text-xs font-bold flex items-center gap-2 transition-all duration-200"
                                        style="background: linear-gradient(135deg, #E07060, #D95B4F); color: white; border: none; cursor: pointer;"
                                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(224,112,96,0.4)'"
                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Reject
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="community-empty-state">
                        <strong>No pending posts</strong>
                        <p>All resident submissions have been reviewed.</p>
                    </div>
                @endforelse
                </div>

                <div class="admin-collapsible-action">
                    @if($pendingCount > 3)
                        <button type="button" class="admin-collapsible-toggle" data-target-list="pending" data-expand-label="See more" data-collapse-label="Show less" aria-expanded="false">See more</button>
                    @else
                        <span class="admin-collapsible-note">You're all caught up</span>
                    @endif
                </div>
            </div>
            <div style="background: #6B4F3A; padding: 6px 24px; border-radius: 0 0 14px 14px;">
                <p style="color: rgba(255, 247, 234, 0.78); font-size: 0.8rem; margin: 0;"></p>
            </div>
        </div>

        {{-- APPROVED POSTS SECTION (Hidden by default) --}}
        <div id="approved-section" class="post-section" style="display: none;">
            <div class="admin-community-review-panel" style="background: linear-gradient(180deg, #2A2C30 0%, #1F2023 100%); border-radius: 32px; padding: 28px 32px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.4); border: 1px solid #3A342D;">
                <div class="admin-brown-panel-head" style="display:flex; justify-content:flex-end; align-items:center; gap:16px; margin-bottom: 24px; background: #6B4F3A; padding: 22px 24px 16px;">
                    <span class="admin-community-status admin-community-status-published text-xs px-3 py-1 rounded-full" style="background: rgba(90,138,90,0.15); color: #5A8A5A;">
                        {{ $approvedCount }} published
                    </span>
                </div>

                <div style="height:1px; background: linear-gradient(to right, rgba(214,168,91,0.3), rgba(214,168,91,0.05), transparent); margin-bottom: 24px;"></div>

                <div data-progressive-list>
                @forelse($approvedPosts as $post)
                    <div class="approved-item admin-community-review-card" data-progressive-item style="background: linear-gradient(135deg, #2C2C2F 0%, #25272A 100%); border: 1px solid rgba(90,138,90,0.3); border-radius: 20px; padding: 20px 24px; margin-bottom: 12px; transition: all 0.3s ease;"
                    onmouseover="this.style.transform='translateX(4px)'; this.style.borderColor='rgba(90,138,90,0.5)';">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-lg">✅</span>
                                    <h3 style="font-size: 16px; font-weight: 700; color: #F8F3EA;">{{ Str::limit($post->title, 60) }}</h3>
                                    <span class="admin-community-status admin-community-status-published text-xs px-2 py-1 rounded-full" style="background: rgba(90,138,90,0.2); color: #5A8A5A;">Published</span>
                                </div>
                                <div class="text-sm" style="color: #B0A898;">
                                    {{ Str::limit($post->content, 100) }}
                                </div>
                                <div class="text-xs mt-2" style="color: #8A7A66;">by {{ $post->user->name ?? 'Resident' }} • {{ $post->created_at->diffForHumans() }}</div>
                            </div>
                            <div class="flex gap-2 items-center justify-center flex-wrap">
                                <form method="POST" action="{{ route('community.destroy', $post) }}" data-confirm-message="Delete this post? This action cannot be undone.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="admin-community-action admin-community-action-danger px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200" style="background: rgba(224,112,96,0.15); color: #E07060; border: none; cursor: pointer;">
                                        🗑️ Delete
                                    </button>
                                </form>
                                <a href="{{ route('community.show', $post) }}" class="admin-community-action admin-community-action-secondary px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 text-decoration-none" style="background: rgba(214,168,91,0.15); color: #D6A85B;">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <p style="color: #8A7A66;">No published posts yet.</p>
                    </div>
                @endforelse
                </div>

                <div class="admin-collapsible-action">
                    @if($approvedCount > 3)
                        <button type="button" class="admin-collapsible-toggle" data-target-list="approved" data-expand-label="See more" data-collapse-label="Show less" aria-expanded="false">See more</button>
                    @else
                        <span class="admin-collapsible-note">You're all caught up</span>
                    @endif
                </div>
            </div>
            <div style="background: #6B4F3A; padding: 6px 24px; border-radius: 0 0 14px 14px;">
                <p style="color: rgba(255, 247, 234, 0.78); font-size: 0.8rem; margin: 0;"></p>
            </div>
        </div>

        {{-- REJECTED POSTS SECTION (Hidden by default) --}}
        <div id="rejected-section" class="post-section" style="display: none;">
            <div class="admin-community-review-panel" style="background: linear-gradient(180deg, #2A2C30 0%, #1F2023 100%); border-radius: 32px; padding: 28px 32px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.4); border: 1px solid #3A342D;">
                <div class="admin-brown-panel-head" style="display:flex; justify-content:flex-end; align-items:center; gap:16px; margin-bottom: 24px; background: #6B4F3A; padding: 22px 24px 16px;">
                    <span class="admin-community-status admin-community-status-rejected text-xs px-3 py-1 rounded-full" style="background: rgba(224,112,96,0.15); color: #E07060;">
                        {{ $rejectedCount }} rejected
                    </span>
                </div>

                <div style="height:1px; background: linear-gradient(to right, rgba(214,168,91,0.3), rgba(214,168,91,0.05), transparent); margin-bottom: 24px;"></div>

                <div data-progressive-list>
                @forelse($rejectedPosts as $post)
                    <div class="rejected-item admin-community-review-card" data-progressive-item style="background: linear-gradient(135deg, #2C2C2F 0%, #25272A 100%); border: 1px solid rgba(224,112,96,0.3); border-radius: 20px; padding: 20px 24px; margin-bottom: 12px; transition: all 0.3s ease;">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-lg">❌</span>
                                    <h3 style="font-size: 16px; font-weight: 700; color: #F8F3EA;">{{ Str::limit($post->title, 60) }}</h3>
                                    <span class="admin-community-status admin-community-status-rejected text-xs px-2 py-1 rounded-full" style="background: rgba(224,112,96,0.2); color: #E07060;">Rejected</span>
                                </div>
                                <div class="text-sm" style="color: #B0A898;">
                                    {{ Str::limit($post->content, 100) }}
                                </div>
                                @if($post->rejection_reason)
                                    <div class="text-xs mt-2" style="color: #E07060;">
                                        Reason: {{ $post->rejection_reason }}
                                    </div>
                                @endif
                                <div class="text-xs mt-2" style="color: #8A7A66;">by {{ $post->user->name ?? 'Resident' }} • {{ $post->created_at->diffForHumans() }}</div>
                            </div>
                            <div class="flex gap-2 items-center justify-center flex-wrap">
                                <form method="POST" action="{{ route('community.destroy', $post) }}" data-confirm-message="Delete this post? This action cannot be undone.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="admin-community-action admin-community-action-danger px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200" style="background: rgba(224,112,96,0.15); color: #E07060; border: none; cursor: pointer;">
                                        🗑️ Delete
                                    </button>
                                </form>
                                <a href="{{ route('community.show', $post) }}" class="admin-community-action admin-community-action-secondary px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 text-decoration-none" style="background: rgba(214,168,91,0.15); color: #D6A85B;">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="community-empty-state">
                        <strong>No rejected posts</strong>
                        <p>Declined submissions will appear here.</p>
                    </div>
                @endforelse
                </div>

                <div class="admin-collapsible-action">
                    @if($rejectedCount > 3)
                        <button type="button" class="admin-collapsible-toggle" data-target-list="rejected" data-expand-label="See more" data-collapse-label="Show less" aria-expanded="false">See more</button>
                    @else
                        <span class="admin-collapsible-note">You're all caught up</span>
                    @endif
                </div>
            </div>
            <div style="background: #6B4F3A; padding: 6px 24px; border-radius: 0 0 14px 14px;">
                <p style="color: rgba(255, 247, 234, 0.78); font-size: 0.8rem; margin: 0;"></p>
            </div>
        </div>
    </div>

    {{-- REJECT MODAL --}}
    <div id="rejectModal" class="community-reject-modal" role="dialog" aria-modal="true" aria-hidden="true">
        <div class="community-reject-modal-card">
            <div class="community-reject-modal-head">
                <h3>Reject Post</h3>
                <button type="button" onclick="closeRejectModal()" class="community-reject-modal-close">&times;</button>
            </div>
            <form id="rejectForm" method="POST" data-prevent-double-submit>
                @csrf
                <label class="community-reject-label">
                    Reason for rejection <span style="font-weight: 400; color: #9b8d81;">(optional)</span>
                </label>
                <textarea name="rejection_reason" rows="4" class="community-reject-textarea" placeholder="Describe why this post doesn't meet community guidelines…"></textarea>
                <div class="community-reject-actions">
                    <button type="submit" class="community-reject-btn community-reject-btn-confirm">Reject Post</button>
                    <button type="button" onclick="closeRejectModal()" class="community-reject-btn community-reject-btn-cancel">Cancel</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
let currentPostId = null;

function filterByStatus(status, activeButton = null) {
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.classList.toggle('is-active', tab === activeButton);
        tab.classList.toggle('active', tab === activeButton);
        tab.setAttribute('aria-selected', tab === activeButton ? 'true' : 'false');
    });

    document.getElementById('pending-section').style.display = status === 'pending' ? 'block' : 'none';
    document.getElementById('approved-section').style.display = status === 'approved' ? 'block' : 'none';
    document.getElementById('rejected-section').style.display = status === 'rejected' ? 'block' : 'none';
}

function openRejectModal(postId) {
    currentPostId = postId;
    const form = document.getElementById('rejectForm');
    form.action = `/community/${postId}/reject`;
    const modal = document.getElementById('rejectModal');
    document.body.appendChild(modal);
    modal.setAttribute('aria-hidden', 'false');
    modal.classList.add('is-open');
    modal.querySelector('textarea')?.focus();
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.setAttribute('aria-hidden', 'true');
    modal.classList.remove('is-open');
    currentPostId = null;
}

document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') closeRejectModal();
});

document.querySelectorAll('.admin-collapsible-toggle').forEach((button) => {
    button.addEventListener('click', () => {
        const target = button.dataset.targetList;
        const items = document.querySelectorAll(`[data-collapsible-item="${target}"]`);
        const expanded = button.dataset.expanded === 'true';

        items.forEach((item, index) => {
            if (index >= 3) {
                item.classList.toggle('is-hidden-by-default', expanded);
                item.style.display = '';
            }
        });

        button.dataset.expanded = expanded ? 'false' : 'true';
        button.setAttribute('aria-expanded', expanded ? 'false' : 'true');
        button.textContent = expanded ? button.dataset.expandLabel : button.dataset.collapseLabel;
    });
});
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap');

.admin-shell {
    max-width: 1580px;
    width: 100%;
    margin: 0 auto;
}

/* Specificity layer targeting to bypass app.css override patterns */
.admin-community-page .admin-community-tabs {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 3px !important;
    margin: 0 0 20px !important;
    padding: 0 !important;
    border: 1px solid rgba(107, 79, 58, 0.20) !important;
    border-radius: 14px !important;
    background: #1F2023 !important; 
    overflow: hidden !important;
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3) !important;
}

.admin-community-page .admin-community-tab {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    min-height: 48px !important;
    padding: 12px 18px !important;
    border: 0 !important;
    border-bottom: 2px solid transparent !important;
    border-radius: 0 !important;
    background: transparent !important;
    color: #8A7A66 !important; 
    font-family: 'Inter', system-ui, sans-serif !important;
    font-size: 0.95rem !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    transition: all 0.2s ease-in-out !important;
}

.admin-community-page .admin-community-tab svg {
    display: block !important;
    width: 16px !important;
    height: 16px !important;
    flex: 0 0 16px !important;
    stroke: currentColor !important;
}

.admin-community-page .admin-community-tab span {
    font-size: 0.8rem !important;
    padding: 2px 6px !important;
    border-radius: 8px !important;
    background: rgba(255, 255, 255, 0.08) !important;
    color: #B0A898 !important;
}

.admin-community-page .admin-community-tab:hover {
    background: rgba(255, 255, 255, 0.02) !important;
    color: #F8F3EA !important;
}

/* Active tab state enforcement */
.admin-community-page .admin-community-tab.is-active,
.admin-community-page .admin-community-tab.active {
    color: #D6A85B !important; 
    border-bottom: 2px solid #D6A85B !important;
    background: transparent !important;
}

.admin-community-page .admin-community-tab.is-active span,
.admin-community-page .admin-community-tab.active span {
    background: rgba(214, 168, 91, 0.15) !important;
    color: #D6A85B !important;
}



.admin-community-review-panel .admin-brown-panel-head p {
    color: rgba(255, 247, 234, 0.78) !important;
    font-size: 12px !important;
}

.admin-community-review-card {
    border: 1px solid #e3d8ca !important;
    border-radius: 10px !important;
    background: #fbf8f3 !important;
    padding: 15px 16px !important;
}

.admin-community-clean-hidden {
    display: none !important;
}

.admin-community-clean-meta {
    color: #9b8d81;
    font-size: .68rem;
    font-weight: 700;
}

.admin-community-clean-title {
    margin: 7px 0 4px;
    color: #342a23;
    font-size: .95rem;
}

.admin-community-clean-copy {
    margin: 0;
    color: #786b60;
    font-size: .8rem;
}

.community-empty-state {
    padding: 36px 20px;
    text-align: center;
    border: 1px dashed rgba(214, 168, 91, 0.2);
    border-radius: 12px;
}

.community-reject-modal {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9000;
    background: rgba(10, 9, 7, 0.62);
    backdrop-filter: blur(4px);
    align-items: center;
    justify-content: center;
}

.community-reject-modal.is-open {
    display: flex;
}

.community-reject-modal-card {
    background: #fff;
    border-radius: 20px;
    padding: 28px;
    max-width: 480px;
    width: 100%;
}

.community-reject-textarea {
    width: 100%;
    padding: 11px 13px;
    border: 1px solid #ddd5c9;
    border-radius: 10px;
}

.admin-collapsible-action {
    display: flex;
    justify-content: flex-end;
    padding: 8px 18px;
    border-top: 1px solid rgba(255, 247, 234, 0.14);
    background: #6B4F3A;
}

.admin-collapsible-toggle {
    display: inline-flex;
    min-height: 34px;
    align-items: center;
    gap: 7px;
    padding: 7px 11px;
    border: 1px solid rgba(255, 247, 234, 0.22);
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.06);
    color: #fff7ea;
    font-size: 0.76rem;
    font-weight: 800;
    cursor: pointer;
    transition: background 0.18s ease, border-color 0.18s ease, transform 0.18s ease;
}

.admin-collapsible-toggle:hover {
    border-color: rgba(255, 247, 234, 0.34);
    background: rgba(255, 255, 255, 0.12);
    transform: translateY(-1px);
}

.admin-collapsible-note {
    color: rgba(255, 247, 234, 0.6);
    font-size: 0.76rem;
    font-weight: 700;
    letter-spacing: 0.02em;
    font-family: 'Inter', system-ui, sans-serif;
}

.post-item.is-hidden-by-default,
.approved-item.is-hidden-by-default,
.rejected-item.is-hidden-by-default {
    display: none !important;
}

.admin-shell > div:first-of-type {
    position: relative !important;
    overflow: hidden !important;
    border-radius: 20px !important;
    background: linear-gradient(120deg, #111009 0%, #1C1A12 50%, #201E14 100%) !important;
    border: 1px solid rgba(214, 168, 91, 0.18) !important;
}

@media (max-width:768px) {
    .admin-shell > div:first-of-type {
        padding: 24px !important;
    }
}
</style>
</x-app-layout>