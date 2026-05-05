<x-app-layout>
    <div class="space-y-8 admin-shell admin-community-page">
        {{-- PAGE HEADER --}}
        <div class="relative overflow-hidden rounded-[36px] border border-[#3A342D]"
             style="
                background:
                    linear-gradient(115deg,
                        #1F2023 0%,
                        #24262B 38%,
                        #2C2C2F 62%,
                        #3B3023 100%);
                box-shadow: 0 18px 50px rgba(0, 0, 0, 0.18);
             ">

            <div class="absolute top-[-90px] right-[10%] w-[320px] h-[320px] rounded-full blur-3xl opacity-20"
                 style="background: rgba(199, 151, 69, 0.35);"></div>

            <div class="absolute bottom-[-120px] left-[18%] w-[260px] h-[260px] rounded-full blur-3xl opacity-10"
                 style="background: rgba(255,255,255,0.18);"></div>

            <div class="relative z-10 px-8 py-10 md:px-14 md:py-12">
                <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                    <div class="max-w-2xl">
                        <div class="mb-3">
                            <span class="inline-block text-[11px] tracking-[0.30em] uppercase"
                                  style="color: #D2A04C; font-weight: 700;">
                                Community Moderation
                            </span>
                        </div>

                        <h1 class="text-4xl md:text-5xl font-bold leading-[1.05] mb-4"
                            style="font-family: 'Playfair Display', serif; color: #F8F3EA;">
                            Resident<br>
                            <span style="color: #F3E5CF;">Posts</span>
                        </h1>

                        <p class="text-base md:text-lg leading-relaxed max-w-xl"
                           style="color: rgba(255,255,255,0.82);">
                            Review resident submissions, approve quality content,
                            reject inappropriate posts, and maintain positive community standards.
                        </p>
                    </div>

                    <div class="shrink-0 flex gap-3">
                        <span style="display: flex; align-items: center; gap: 8px; padding: 13px 24px; background: rgba(255,255,255,0.08); border: 1px solid rgba(214,168,91,0.28); border-radius: 999px; color: #F2DEC0; font-weight: 600;">
                            Pending: {{ $pendingPosts->total() ?? 0 }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- QUICK STATS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 admin-feature-stat-grid">
            <div style="background: linear-gradient(180deg, #25272C 0%, #1F2023 100%); border: 1px solid rgba(214,168,91,0.14); border-radius: 22px; padding: 22px 24px;">
                <div style="display:flex; align-items:end; justify-content:space-between; gap:12px;">
                    <div style="font-size: 32px; font-weight: 800; color: #F0B3A9; line-height: 1;">{{ $pendingPosts->total() }}</div>
                    <div style="font-size: 11px; color: #8A7A66; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase;">Queue</div>
                </div>
                <div style="font-size: 11px; color: #D2A04C; text-transform: uppercase; letter-spacing: 0.18em; font-weight: 700; margin-top: 10px;">Awaiting Review</div>
                <div style="font-size: 12px; color: #8A7A66; margin-top: 6px;">Posts pending moderation</div>
            </div>
            <div style="background: linear-gradient(180deg, #25272C 0%, #1F2023 100%); border: 1px solid rgba(214,168,91,0.14); border-radius: 22px; padding: 22px 24px;">
                <div style="display:flex; align-items:end; justify-content:space-between; gap:12px;">
                    <div style="font-size: 32px; font-weight: 800; color: #A8CAA8; line-height: 1;">{{ $approvedPosts->count() }}</div>
                    <div style="font-size: 11px; color: #8A7A66; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase;">Live</div>
                </div>
                <div style="font-size: 11px; color: #D2A04C; text-transform: uppercase; letter-spacing: 0.18em; font-weight: 700; margin-top: 10px;">Published</div>
                <div style="font-size: 12px; color: #8A7A66; margin-top: 6px;">Visible in the community feed</div>
            </div>
            <div style="background: linear-gradient(180deg, #25272C 0%, #1F2023 100%); border: 1px solid rgba(214,168,91,0.14); border-radius: 22px; padding: 22px 24px;">
                <div style="display:flex; align-items:end; justify-content:space-between; gap:12px;">
                    <div style="font-size: 32px; font-weight: 800; color: #D7B48D; line-height: 1;">{{ $rejectedPosts->count() }}</div>
                    <div style="font-size: 11px; color: #8A7A66; font-weight: 700; letter-spacing: 0.14em; text-transform: uppercase;">Review</div>
                </div>
                <div style="font-size: 11px; color: #D2A04C; text-transform: uppercase; letter-spacing: 0.18em; font-weight: 700; margin-top: 10px;">Rejected</div>
                <div style="font-size: 12px; color: #8A7A66; margin-top: 6px;">Filtered out by moderation</div>
            </div>
        </div>

        {{-- PENDING POSTS --}}
        <div style="
            background: linear-gradient(180deg, #2A2C30 0%, #1F2023 100%);
            border-radius: 32px;
            padding: 40px;
            box-shadow: 0 16px 48px rgba(0,0,0,0.4);
            border: 1px solid #3A342D;
        ">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px;">
                <h2 style="
                    font-size: 28px;
                    font-weight: 600;
                    color: #F8F3EA;
                    font-family: 'Playfair Display', serif;
                ">
                    Posts Awaiting Review
                </h2>
                <div class="flex gap-3">
                    @if($pendingPosts->count() > 0)
                        <span style="padding: 8px 16px; border-radius: 999px; font-size: 12px; font-weight: 700; background: rgba(224,112,96,0.2); color: #E07060;">
                            {{ $pendingPosts->total() }} pending
                        </span>
                    @endif
                </div>
            </div>

            @forelse($pendingPosts as $post)
                <div style="
                    background: linear-gradient(135deg, #2C2C2F 0%, #25272A 100%);
                    border: 1px solid rgba(58,52,45,0.6);
                    border-radius: 24px;
                    padding: 32px;
                    margin-bottom: 24px;
                    transition: all 0.3s ease;
                " onmouseover="this.style.boxShadow='0 20px 48px rgba(0,0,0,0.5)'" onmouseout="this.style.boxShadow='0 8px 32px rgba(0,0,0,0.3)'">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:gap-6">
                        <div class="flex flex-col gap-4 flex-1">
                            <div class="flex items-center gap-4">
                                <h3 style="font-size: 24px; font-weight: 700; color: #F8F3EA; margin: 0; line-height: 1.2;">
                                    {{ $post->title }}
                                </h3>
                                <span style="padding: 8px 16px; border-radius: 999px; font-size: 12px; font-weight: 700; background: rgba(224,112,96,0.2); color: #E07060;">
                                    {{ ucfirst($post->type) }}
                                </span>
                            </div>

                            <p style="font-size: 16px; color: #D0C8B8; line-height: 1.7; max-width: 700px;">
                                {{ Str::limit($post->content, 250) }}
                            </p>

                            @if($post->image_path)
                                <img src="{{ asset('storage/' . $post->image_path) }}" alt="Post image" style="max-width: 300px; max-height: 200px; border-radius: 16px; object-fit: cover; box-shadow: 0 8px 24px rgba(0,0,0,0.3);">
                            @endif

                            <div style="display: flex; gap: 16px; align-items: center; font-size: 14px; color: #A89F91;">
                                <span>👤 {{ $post->user->name ?? 'User' }}</span>
                                <span style="width: 1px; height: 16px; background: rgba(168,159,145,0.4);"></span>
                                <span>📅 {{ $post->created_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        <div style="flex-shrink: 0; margin-top: 20px; lg:mt: 0;">
                            <div style="display: flex; gap: 12px; flex-direction: column; align-items: stretch;">

<form method="POST" action="{{ route('community.approve', $post) }}">
    @csrf
    <button type="submit" style="
        background: linear-gradient(135deg, #5A8A5A, #6DA76D);
        color: white;
        padding: 14px 28px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 13px;
        border: none;
        cursor: pointer;
        box-shadow: 0 8px 24px rgba(90,138,90,0.3);
    ">
        ✅ Approve & Publish
    </button>
</form>

                                <button onclick="openRejectModal({{ $post->id }})" style="
                                    background: linear-gradient(135deg, #E07060, #D95B4F); 
                                    color: white; 
                                    padding: 14px 28px; 
                                    border-radius: 999px; 
                                    font-weight: 700; 
                                    font-size: 13px; 
                                    text-align: center; 
                                    border: none;
                                    cursor: pointer; 
                                    box-shadow: 0 8px 24px rgba(224,112,96,0.3); 
                                ">
                                    ❌ Reject
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 80px 60px; background: linear-gradient(135deg, rgba(37,39,42,0.6), rgba(31,32,35,0.6)); border: 1px dashed rgba(214,168,91,0.25); border-radius: 32px;">
                    <div style="width: 88px; height: 88px; border-radius: 999px; margin: 0 auto 32px; display:flex; align-items:center; justify-content:center; background: rgba(214,168,91,0.15); font-size: 44px;">
                        💬
                    </div>
                    <h3 style="font-size: 32px; font-weight: 700; color: #F8F3EA; margin-bottom: 16px; font-family: 'Playfair Display', serif;">
                        No pending posts
                    </h3>
                    <p style="color: #D0C8B8; font-size: 18px; margin-bottom: 40px;">
                        Community is flowing smoothly. All resident posts are approved.
                    </p>
                </div>
            @endforelse

            {{-- Pagination --}}
            <div style="margin-top: 48px;">
                {{ $pendingPosts->links() }}
            </div>
        </div>

        {{-- REJECT MODAL --}}
        <div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-60 hidden flex items-center justify-center z-50 backdrop-blur-sm">
            <div style="background: linear-gradient(135deg, #2A2C30 0%, #1F2023 100%); border: 1px solid #3A342D; border-radius: 24px; padding: 40px; max-width: 500px; width: 90%;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <h3 style="font-size: 24px; font-weight: 700; color: #F8F3EA; margin: 0;">
                        Reject Post
                    </h3>
                    <button onclick="closeRejectModal()" style="color: #D0C8B8; font-size: 24px; cursor: pointer;">×</button>
                </div>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div style="margin-bottom: 24px;">
                        <label style="display: block; font-size: 14px; font-weight: 600; color: #D0C8B8; margin-bottom: 12px;">
                            Reason for rejection (optional but recommended)
                        </label>
                        <textarea name="rejection_reason" rows="4" class="w-full bg-[rgba(37,39,42,0.8)] border border-[rgba(58,52,45,0.8)] rounded-xl px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#D6A85B] resize-vertical" placeholder="This post violates community guidelines by..."></textarea>
                    </div>
                    <div style="display: flex; gap: 16px; justify-content: flex-end;">
                        <button type="button" onclick="closeRejectModal()" style="padding: 14px 28px; background: rgba(168,159,145,0.2); color: #D0C8B8; border: 1px solid rgba(168,159,145,0.3); border-radius: 12px; font-weight: 600; cursor: pointer;">
                            Cancel
                        </button>
                        <button type="submit" style="padding: 14px 28px; background: linear-gradient(90deg, #E07060, #D95B4F); color: white; border: none; border-radius: 12px; font-weight: 700; cursor: pointer; box-shadow: 0 8px 24px rgba(224,112,96,0.3);">
                            Reject Post
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
let currentPostId = null;

function openRejectModal(postId) {
    currentPostId = postId;
    document.getElementById('rejectForm').action = `/community/${postId}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    currentPostId = null;
}
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600;700&display=swap');

.admin-shell {
    max-width: 1580px;
    width: 100%;
    margin: 0 auto;
}

.admin-shell > div:first-of-type {
    position: relative !important;
    overflow: hidden !important;
    border-radius: 20px !important;
    background: linear-gradient(120deg, #111009 0%, #1C1A12 50%, #201E14 100%) !important;
    border: 1px solid rgba(214,168,91,0.18) !important;
    box-shadow: none !important;
}

.admin-shell > div:first-of-type::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(214,168,91,0.04) 1px, transparent 1px),
        linear-gradient(90deg, rgba(214,168,91,0.04) 1px, transparent 1px);
    background-size: 48px 48px;
    pointer-events: none;
}

.admin-shell > div:first-of-type > div.absolute:first-child {
    top: -60px !important;
    right: -40px !important;
    width: 280px !important;
    height: 280px !important;
    background: radial-gradient(circle, rgba(214,168,91,0.15) 0%, transparent 70%) !important;
    opacity: 1 !important;
    filter: none !important;
}

.admin-shell > div:first-of-type > div.absolute:nth-child(2) {
    display: none !important;
}

.admin-shell > div:first-of-type > div.relative {
    padding: 36px 44px !important;
}

.admin-shell > div:first-of-type > div.relative > div {
    align-items: center !important;
}

.admin-shell > div:first-of-type .mb-3 {
    margin-bottom: 12px !important;
}

.admin-shell > div:first-of-type .mb-3 span {
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    font-size: 0.875rem !important;
    letter-spacing: 0.18em !important;
    text-transform: uppercase !important;
    color: #d6a85b !important;
    font-weight: 700 !important;
}

.admin-shell > div:first-of-type .mb-3 span::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 999px;
    background: #d6a85b;
    display: inline-block;
}

.admin-shell > div:first-of-type h1 {
    color: #f0e9df !important;
    font-family: 'Playfair Display', serif !important;
    font-size: clamp(2.5rem, 4vw, 3.5rem) !important;
    font-weight: 700 !important;
    line-height: 1.12 !important;
    margin-bottom: 12px !important;
}

.admin-shell > div:first-of-type p {
    color: rgba(255,255,255,0.62) !important;
    font-size: 1.125rem !important;
    max-width: 760px !important;
}

@media (max-width: 768px) {
    .admin-shell > div:first-of-type > div.relative {
        padding: 24px !important;
    }
}
</style>

</x-app-layout> 
