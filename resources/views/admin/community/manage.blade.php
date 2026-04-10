<x-app-layout>
<div class="space-y-8 admin-shell">

    {{-- PREMIUM ADMIN HEADER --}}
    <div class="relative overflow-hidden rounded-[36px] border border-[#3A342D]"
         style="background: linear-gradient(115deg, #1A1C1E 0%, #1F2023 38%, #24262B 62%, #2C2C2F 100%); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">

        <div class="absolute top-[-90px] right-[10%] w-[320px] h-[320px] rounded-full blur-3xl opacity-20"
             style="background: rgba(199, 151, 69, 0.3);"></div>

        <div class="absolute bottom-[-120px] left-[18%] w-[260px] h-[260px] rounded-full blur-3xl opacity-10"
             style="background: rgba(255,255,255,0.08);"></div>

        <div class="relative z-10 px-8 py-10 md:px-14 md:py-12">
            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div class="max-w-2xl">
                    <div class="mb-3 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background: linear-gradient(135deg, #D6A85B, #B8842F);">
                            <svg class="w-4 h-4" fill="none" stroke="white" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                            </svg>
                        </div>
                        <span class="inline-block text-[11px] tracking-[0.30em] uppercase"
                              style="color: #D6A85B; font-weight: 700;">
                            Community Moderation
                        </span>
                    </div>

                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-[1.05] mb-4"
                        style="font-family: 'Playfair Display', serif; color: #F8F3EA;">
                        Resident<br>
                        <span style="color: #D6A85B;">Community Hub</span>
                    </h1>

                    <p class="text-base md:text-lg leading-relaxed max-w-xl"
                       style="color: rgba(255,255,255,0.7);">
                        Review resident submissions, approve quality content,
                        reject inappropriate posts, and maintain positive community standards.
                    </p>
                </div>

                <div class="shrink-0 flex items-center gap-3 px-4 py-2 rounded-full"
                     style="background: rgba(214,168,91,0.1); border: 1px solid rgba(214,168,91,0.2);">
                    <span class="text-xs font-mono" style="color: #D6A85B;">👑 Moderation Panel</span>
                </div>
            </div>
        </div>
    </div>

    {{-- STATS CARDS --}}
    @php
        $pendingCount = $pendingPosts->total();
        $approvedCount = $approvedPosts->count();
        $rejectedCount = $rejectedPosts->count();
        $totalCount = $pendingCount + $approvedCount + $rejectedCount;
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="group relative overflow-hidden rounded-2xl transition-all duration-300 hover:scale-[1.02]"
             style="background: linear-gradient(135deg, #1A1C1E 0%, #24262B 100%); border: 1px solid rgba(214,168,91,0.15); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);">
            <div class="absolute top-0 right-0 w-24 h-24 -translate-y-8 translate-x-8 rounded-full blur-3xl opacity-20 transition-opacity duration-300 group-hover:opacity-40" style="background: #D6A85B;"></div>
            <div class="relative p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: rgba(214,168,91,0.15);">
                        <svg class="w-6 h-6" style="color: #D6A85B;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <span class="text-4xl font-bold" style="color: #D6A85B;">{{ $totalCount }}</span>
                </div>
                <div class="text-xs font-semibold uppercase tracking-wider" style="color: #8A7A66;">Total Posts</div>
                <div class="text-xs mt-1" style="color: #6B5B4A;">All time submissions</div>
            </div>
        </div>

        <div class="group relative overflow-hidden rounded-2xl transition-all duration-300 hover:scale-[1.02]"
             style="background: linear-gradient(135deg, #1A1C1E 0%, #24262B 100%); border: 1px solid rgba(224,112,96,0.15); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);">
            <div class="absolute top-0 right-0 w-24 h-24 -translate-y-8 translate-x-8 rounded-full blur-3xl opacity-20 transition-opacity duration-300 group-hover:opacity-40" style="background: #E07060;"></div>
            <div class="relative p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: rgba(224,112,96,0.15);">
                        <svg class="w-6 h-6" style="color: #E07060;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-4xl font-bold" style="color: #E07060;">{{ $pendingCount }}</span>
                </div>
                <div class="text-xs font-semibold uppercase tracking-wider" style="color: #8A7A66;">Pending Review</div>
                <div class="text-xs mt-1" style="color: #6B5B4A;">Awaiting moderation</div>
            </div>
        </div>

        <div class="group relative overflow-hidden rounded-2xl transition-all duration-300 hover:scale-[1.02]"
             style="background: linear-gradient(135deg, #1A1C1E 0%, #24262B 100%); border: 1px solid rgba(90,138,90,0.15); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);">
            <div class="absolute top-0 right-0 w-24 h-24 -translate-y-8 translate-x-8 rounded-full blur-3xl opacity-20 transition-opacity duration-300 group-hover:opacity-40" style="background: #5A8A5A;"></div>
            <div class="relative p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: rgba(90,138,90,0.15);">
                        <svg class="w-6 h-6" style="color: #5A8A5A;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-4xl font-bold" style="color: #5A8A5A;">{{ $approvedCount }}</span>
                </div>
                <div class="text-xs font-semibold uppercase tracking-wider" style="color: #8A7A66;">Published</div>
                <div class="text-xs mt-1" style="color: #6B5B4A;">Approved content</div>
            </div>
        </div>

        <div class="group relative overflow-hidden rounded-2xl transition-all duration-300 hover:scale-[1.02]"
             style="background: linear-gradient(135deg, #1A1C1E 0%, #24262B 100%); border: 1px solid rgba(190,147,96,0.15); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);">
            <div class="absolute top-0 right-0 w-24 h-24 -translate-y-8 translate-x-8 rounded-full blur-3xl opacity-20 transition-opacity duration-300 group-hover:opacity-40" style="background: #BE9360;"></div>
            <div class="relative p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: rgba(190,147,96,0.15);">
                        <svg class="w-6 h-6" style="color: #BE9360;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m0 0A9.001 9.001 0 006.972 5.649l4.728-4.728 4.728 4.728A9.001 9.001 0 0018.364 5.636m0 0c-.317.301-.533.677-.533 1.118V15.5a2.25 2.25 0 01-2.25 2.25H9.25a2.25 2.25 0 01-2.25-2.25v-1.722c0-.441.217-.817.533-1.118z"></path>
                        </svg>
                    </div>
                    <span class="text-4xl font-bold" style="color: #BE9360;">{{ $rejectedCount }}</span>
                </div>
                <div class="text-xs font-semibold uppercase tracking-wider" style="color: #8A7A66;">Rejected</div>
                <div class="text-xs mt-1" style="color: #6B5B4A;">Declined submissions</div>
            </div>
        </div>
    </div>

    {{-- TABS FOR DIFFERENT STATUSES --}}
    <div class="flex flex-wrap gap-2 border-b pb-3" style="border-color: rgba(214,168,91,0.2);">
        <button onclick="filterByStatus('pending')" class="filter-tab active px-5 py-2 rounded-full text-sm font-medium transition-all duration-200" style="background: rgba(214,168,91,0.2); color: #D6A85B;">
            Pending ({{ $pendingCount }})
        </button>
        <button onclick="filterByStatus('approved')" class="filter-tab px-5 py-2 rounded-full text-sm font-medium transition-all duration-200" style="color: #B0A898;">
            Published ({{ $approvedCount }})
        </button>
        <button onclick="filterByStatus('rejected')" class="filter-tab px-5 py-2 rounded-full text-sm font-medium transition-all duration-200" style="color: #B0A898;">
            Rejected ({{ $rejectedCount }})
        </button>
    </div>

    {{-- PENDING POSTS SECTION --}}
    <div id="pending-section" class="post-section">
        <div style="
            background: linear-gradient(180deg, #2A2C30 0%, #1F2023 100%);
            border-radius: 32px;
            padding: 28px 32px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.4);
            border: 1px solid #3A342D;
        ">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom: 24px; flex-wrap: wrap;">
                <div>
                    <h2 style="
                        font-size: 24px;
                        font-weight: 600;
                        color: #F8F3EA;
                        font-family: 'Playfair Display', serif;
                        margin: 0;
                    ">
                        Posts Awaiting Review
                    </h2>
                    <p style="font-size: 12px; color: #8A7A66; margin-top: 4px;">Review and moderate resident submissions</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs px-3 py-1 rounded-full" style="background: rgba(224,112,96,0.15); color: #E07060;">
                        {{ $pendingCount }} pending approval
                    </span>
                </div>
            </div>

            <div style="height:1px; background: linear-gradient(to right, rgba(214,168,91,0.3), rgba(214,168,91,0.05), transparent); margin-bottom: 24px;"></div>

            @forelse($pendingPosts as $post)
                <div class="post-item" data-status="pending"
                     style="
                        background: linear-gradient(135deg, #2C2C2F 0%, #25272A 100%);
                        border: 1px solid rgba(58,52,45,0.6);
                        border-radius: 24px;
                        padding: 24px 28px;
                        margin-bottom: 20px;
                        transition: all 0.3s ease;
                     "
                     onmouseover="this.style.transform='translateX(4px)'; this.style.borderColor='rgba(214,168,91,0.4)'; this.style.boxShadow='0 12px 32px rgba(0,0,0,0.4)';"
                     onmouseout="this.style.transform='translateX(0)'; this.style.borderColor='rgba(58,52,45,0.6)'; this.style.boxShadow='none';">

                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-4 mb-4">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl" style="background: rgba(214,168,91,0.12);">
                                    @if($post->type === 'lost_found') 🔍
                                    @elseif($post->type === 'buy_sell') 💰
                                    @elseif($post->type === 'event') 🎉
                                    @else 💬
                                    @endif
                                </div>
                                <div>
                                    <h3 style="font-size: 20px; font-weight: 700; color: #F8F3EA; margin: 0;">
                                        {{ $post->title }}
                                    </h3>
                                    <div class="flex flex-wrap items-center gap-3 mt-2">
                                        <span class="text-xs px-2 py-1 rounded-full" style="background: rgba(214,168,91,0.15); color: #D6A85B;">
                                            {{ strtoupper(str_replace('_', ' ', $post->type)) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <p style="font-size: 15px; color: #B0A898; line-height: 1.6; margin-bottom: 16px;">
                                {{ Str::limit($post->content, 200) }}
                            </p>

                            @if($post->image_path)
                                <div class="mb-4">
                                    <img src="{{ asset('storage/' . $post->image_path) }}" 
                                         alt="Post image" 
                                         style="max-width: 250px; max-height: 180px; border-radius: 16px; object-fit: cover; border: 1px solid rgba(214,168,91,0.2); cursor: pointer;"
                                         onclick="window.open(this.src, '_blank')">
                                </div>
                            @endif

                            @if($post->video_path)
                                <div class="mb-4">
                                    <video controls style="max-width: 250px; max-height: 180px; border-radius: 16px;">
                                        <source src="{{ asset('storage/' . $post->video_path) }}">
                                    </video>
                                </div>
                            @endif

                            <div class="flex flex-wrap items-center gap-4 text-sm" style="color: #8A7A66;">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    {{ $post->user->name ?? 'Resident' }}
                                </div>
                                <div class="w-px h-3" style="background: #5A4A3A;"></div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                    </svg>
                                    #{{ $post->id }}
                                </div>
                                <div class="w-px h-3" style="background: #5A4A3A;"></div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ $post->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3 flex-shrink-0">
                            <a href="{{ route('community.approve', $post) }}" 
                               class="px-6 py-3 rounded-xl font-bold flex items-center gap-2 transition-all duration-200 text-decoration-none"
                               style="background: linear-gradient(135deg, #5A8A5A, #6DA76D); color: white;"
                               onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 20px rgba(90,138,90,0.4)'"
                               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Approve
                            </a>
                            <button onclick="openRejectModal({{ $post->id }})" 
                                    class="px-6 py-3 rounded-xl font-bold flex items-center gap-2 transition-all duration-200"
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
                <div class="text-center py-16">
                    <div class="w-20 h-20 rounded-full mx-auto mb-5 flex items-center justify-center" style="background: rgba(214,168,91,0.1);">
                        <span class="text-4xl">✅</span>
                    </div>
                    <h3 style="font-size: 22px; font-weight: 600; color: #F8F3EA; margin-bottom: 8px;">No pending posts</h3>
                    <p style="color: #8A7A66;">All resident submissions have been reviewed.</p>
                </div>
            @endforelse

            @if($pendingPosts->hasPages())
                <div style="margin-top: 32px;">
                    {{ $pendingPosts->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- APPROVED POSTS SECTION (Hidden by default) --}}
    <div id="approved-section" class="post-section" style="display: none;">
        <div style="
            background: linear-gradient(180deg, #2A2C30 0%, #1F2023 100%);
            border-radius: 32px;
            padding: 28px 32px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.4);
            border: 1px solid #3A342D;
        ">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom: 24px;">
                <h2 style="font-size: 24px; font-weight: 600; color: #F8F3EA; font-family: 'Playfair Display', serif; margin: 0;">
                    Published Posts
                </h2>
                <span class="text-xs px-3 py-1 rounded-full" style="background: rgba(90,138,90,0.15); color: #5A8A5A;">
                    {{ $approvedCount }} published
                </span>
            </div>

            <div style="height:1px; background: linear-gradient(to right, rgba(214,168,91,0.3), rgba(214,168,91,0.05), transparent); margin-bottom: 24px;"></div>

            @forelse($approvedPosts as $post)
                <div class="approved-item" style="
                    background: linear-gradient(135deg, #2C2C2F 0%, #25272A 100%);
                    border: 1px solid rgba(90,138,90,0.3);
                    border-radius: 20px;
                    padding: 20px 24px;
                    margin-bottom: 12px;
                    transition: all 0.3s ease;
                "
                onmouseover="this.style.transform='translateX(4px)'; this.style.borderColor='rgba(90,138,90,0.5)';">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="text-lg">✅</span>
                                <h3 style="font-size: 16px; font-weight: 700; color: #F8F3EA;">{{ Str::limit($post->title, 60) }}</h3>
                                <span class="text-xs px-2 py-1 rounded-full" style="background: rgba(90,138,90,0.2); color: #5A8A5A;">Published</span>
                            </div>
                            <div class="text-sm" style="color: #B0A898;">
                                {{ Str::limit($post->content, 100) }}
                            </div>
                            <div class="text-xs mt-2" style="color: #8A7A66;">by {{ $post->user->name ?? 'Resident' }} • {{ $post->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('community.destroy', $post) }}" onsubmit="return confirm('Delete this post? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200" style="background: rgba(224,112,96,0.15); color: #E07060; border: none; cursor: pointer;" onmouseover="this.style.background='rgba(224,112,96,0.25)'" onmouseout="this.style.background='rgba(224,112,96,0.15)'">
                                    🗑️ Delete
                                </button>
                            </form>
                            <a href="{{ route('community.show', $post) }}" class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 text-decoration-none" style="background: rgba(214,168,91,0.15); color: #D6A85B;" onmouseover="this.style.background='rgba(214,168,91,0.25)'" onmouseout="this.style.background='rgba(214,168,91,0.15)'">
                                View →
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
    </div>

    {{-- REJECTED POSTS SECTION (Hidden by default) --}}
    <div id="rejected-section" class="post-section" style="display: none;">
        <div style="
            background: linear-gradient(180deg, #2A2C30 0%, #1F2023 100%);
            border-radius: 32px;
            padding: 28px 32px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.4);
            border: 1px solid #3A342D;
        ">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom: 24px;">
                <h2 style="font-size: 24px; font-weight: 600; color: #F8F3EA; font-family: 'Playfair Display', serif; margin: 0;">
                    Rejected Posts
                </h2>
                <span class="text-xs px-3 py-1 rounded-full" style="background: rgba(224,112,96,0.15); color: #E07060;">
                    {{ $rejectedCount }} rejected
                </span>
            </div>

            <div style="height:1px; background: linear-gradient(to right, rgba(214,168,91,0.3), rgba(214,168,91,0.05), transparent); margin-bottom: 24px;"></div>

            @forelse($rejectedPosts as $post)
                <div class="rejected-item" style="
                    background: linear-gradient(135deg, #2C2C2F 0%, #25272A 100%);
                    border: 1px solid rgba(224,112,96,0.3);
                    border-radius: 20px;
                    padding: 20px 24px;
                    margin-bottom: 12px;
                    transition: all 0.3s ease;
                ">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="text-lg">❌</span>
                                <h3 style="font-size: 16px; font-weight: 700; color: #F8F3EA;">{{ Str::limit($post->title, 60) }}</h3>
                                <span class="text-xs px-2 py-1 rounded-full" style="background: rgba(224,112,96,0.2); color: #E07060;">Rejected</span>
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
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('community.destroy', $post) }}" onsubmit="return confirm('Delete this post? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200" style="background: rgba(224,112,96,0.15); color: #E07060; border: none; cursor: pointer;" onmouseover="this.style.background='rgba(224,112,96,0.25)'" onmouseout="this.style.background='rgba(224,112,96,0.15)'">
                                    🗑️ Delete
                                </button>
                            </form>
                            <a href="{{ route('community.show', $post) }}" class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 text-decoration-none" style="background: rgba(214,168,91,0.15); color: #D6A85B;" onmouseover="this.style.background='rgba(214,168,91,0.25)'" onmouseout="this.style.background='rgba(214,168,91,0.15)'">
                                View →
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <p style="color: #8A7A66;">No rejected posts.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- REJECT MODAL --}}
    <div id="rejectModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-50 backdrop-blur-sm" style="display: none;">
        <div style="background: linear-gradient(135deg, #2A2C30 0%, #1F2023 100%); border: 1px solid #3A342D; border-radius: 28px; padding: 32px; max-width: 500px; width: 90%;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background: rgba(224,112,96,0.15);">
                        <svg class="w-5 h-5" style="color: #E07060;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <h3 style="font-size: 22px; font-weight: 700; color: #F8F3EA; font-family: 'Playfair Display', serif; margin: 0;">
                        Reject Post
                    </h3>
                </div>
                <button onclick="closeRejectModal()" class="text-3xl cursor-pointer leading-none transition-all duration-200 hover:opacity-70" style="color: #8A7A66; background: none; border: none;">×</button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div style="margin-bottom: 24px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #D0C8B8; margin-bottom: 8px;">
                        Reason for rejection (optional but recommended)
                    </label>
                    <textarea name="rejection_reason" rows="4" class="w-full px-4 py-3 rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[#D6A85B]" style="background: rgba(37,39,42,0.9); border: 1px solid #3A342D; color: #F8F3EA; resize: vertical;" placeholder="This post violates community guidelines because..."></textarea>
                </div>
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" onclick="closeRejectModal()" class="px-6 py-3 rounded-xl font-semibold transition-all duration-200" style="background: rgba(168,159,145,0.1); color: #B0A898; border: 1px solid rgba(168,159,145,0.2); cursor: pointer;" onmouseover="this.style.background='rgba(168,159,145,0.2)'" onmouseout="this.style.background='rgba(168,159,145,0.1)'">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-3 rounded-xl font-bold transition-all duration-200 flex items-center gap-2" style="background: linear-gradient(135deg, #E07060, #D95B4F); color: white; border: none; cursor: pointer; box-shadow: 0 4px 15px rgba(224,112,96,0.3);" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 8px 25px rgba(224,112,96,0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(224,112,96,0.3)'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Reject Post
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
let currentPostId = null;

function filterByStatus(status) {
    // Update active tab styling
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.style.background = 'transparent';
        tab.style.color = '#B0A898';
    });
    event.target.style.background = 'rgba(214,168,91,0.2)';
    event.target.style.color = '#D6A85B';
    
    // Show/hide sections
    document.getElementById('pending-section').style.display = status === 'pending' ? 'block' : 'none';
    document.getElementById('approved-section').style.display = status === 'approved' ? 'block' : 'none';
    document.getElementById('rejected-section').style.display = status === 'rejected' ? 'block' : 'none';
}

function openRejectModal(postId) {
    currentPostId = postId;
    const form = document.getElementById('rejectForm');
    form.action = `/community/${postId}/reject`;
    const modal = document.getElementById('rejectModal');
    modal.style.display = 'flex';
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.style.display = 'none';
    currentPostId = null;
}

// Close modal on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeRejectModal();
    }
});
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap');

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

::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: #2A2C30; border-radius: 10px; }
::-webkit-scrollbar-thumb { background: linear-gradient(#D6A85B, #B8842F); border-radius: 10px; }
::-webkit-scrollbar-thumb:hover { background: linear-gradient(#C49A4A, #A37222); }
</style>
</x-app-layout>
