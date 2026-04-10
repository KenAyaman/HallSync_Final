<x-app-layout>
    <div class="space-y-8">

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
                                Community Notice Details
                            </span>
                        </div>

                        <h1 class="text-4xl md:text-5xl font-bold leading-[1.05] mb-4"
                            style="font-family: 'Playfair Display', serif; color: #F8F3EA;">
                            Announcement<br>
                            <span style="color: #F3E5CF;">Details</span>
                        </h1>

                        <p class="text-base md:text-lg leading-relaxed max-w-xl"
                           style="color: rgba(255,255,255,0.82);">
                            Review the full announcement, its priority level,
                            and the information shared with the community.
                        </p>
                    </div>

                    <div class="shrink-0">
                        <a href="{{ route('announcements.index') }}"
                           style="
                                background: rgba(255,255,255,0.05);
                                border: 1px solid rgba(214,168,91,0.28);
                                color: #F2DEC0;
                                padding: 13px 26px;
                                border-radius: 999px;
                                font-weight: 600;
                                text-decoration: none;
                                backdrop-filter: blur(8px);
                                transition: all 0.3s ease;
                                display: inline-block;
                           "
                           onmouseover="this.style.transform='translateY(-2px)'; this.style.background='rgba(255,255,255,0.09)';"
                           onmouseout="this.style.transform='translateY(0)'; this.style.background='rgba(255,255,255,0.05)';">
                            ← Back to Announcements
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-[1.15fr_0.85fr] gap-8">

            {{-- MAIN DETAILS --}}
            <div style="
                background: linear-gradient(180deg, #FFFFFF 0%, #FDFBF8 100%);
                border-radius: 32px;
                padding: 28px 32px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.04);
                border: 1px solid #3A342D;
            ">
                <div style="display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom: 20px; flex-wrap: wrap;">
                    <h2 style="
                        font-size: 24px;
                        font-weight: 600;
                        color: #2F2A27;
                        font-family: 'Playfair Display', serif;
                        margin: 0;
                    ">
                        Announcement Information
                    </h2>

                    <span style="
                        padding: 7px 14px;
                        border-radius: 999px;
                        font-size: 11px;
                        font-weight: 700;
                        text-transform: uppercase;
                        letter-spacing: 0.08em;
                        background:
                            {{
                                $announcement->priority === 'urgent' ? '#FFF1EE'
                                : ($announcement->priority === 'important' ? '#FEF8EE'
                                : '#F9F1E4')
                            }};
                        color:
                            {{
                                $announcement->priority === 'urgent' ? '#B96A5D'
                                : ($announcement->priority === 'important' ? '#B8842F'
                                : '#A37222')
                            }};
                    ">
                        {{ ucfirst($announcement->priority) }}
                    </span>
                </div>

                <div style="height:1px; background: linear-gradient(to right, #E8D9C5, #F3ECE2, transparent); margin-bottom: 24px;"></div>

                <div class="space-y-4">
                    <div style="background:#FBF8F3; border:1px solid #EFE4D6; border-radius:20px; padding:18px 20px;">
                        <div style="font-size:11px; letter-spacing:0.14em; text-transform:uppercase; color:#B39A78; font-weight:700; margin-bottom:8px;">
                            Title
                        </div>
                        <div style="font-size:20px; font-weight:700; color:#2F2A27;">
                            {{ $announcement->title }}
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div style="background:#FBF8F3; border:1px solid #EFE4D6; border-radius:20px; padding:18px 20px;">
                            <div style="font-size:11px; letter-spacing:0.14em; text-transform:uppercase; color:#B39A78; font-weight:700; margin-bottom:8px;">
                                Posted By
                            </div>
                            <div style="font-size:14px; font-weight:600; color:#3A342D; line-height:1.6;">
                                {{ $announcement->user->name }}
                            </div>
                        </div>

                        <div style="background:#FBF8F3; border:1px solid #EFE4D6; border-radius:20px; padding:18px 20px;">
                            <div style="font-size:11px; letter-spacing:0.14em; text-transform:uppercase; color:#B39A78; font-weight:700; margin-bottom:8px;">
                                Date Posted
                            </div>
                            <div style="font-size:14px; font-weight:600; color:#3A342D; line-height:1.6;">
                                {{ $announcement->created_at->format('F d, Y h:i A') }}
                            </div>
                        </div>
                    </div>

                    <div style="background:#FBF8F3; border:1px solid #EFE4D6; border-radius:20px; padding:18px 20px;">
                        <div style="font-size:11px; letter-spacing:0.14em; text-transform:uppercase; color:#B39A78; font-weight:700; margin-bottom:8px;">
                            Content
                        </div>
                        <div style="font-size:14px; color:#6E665C; line-height:1.9; white-space:pre-line;">
                            {{ $announcement->content }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- SIDE PANEL --}}
            <div class="space-y-6">
                <div style="
                    background: linear-gradient(180deg, #FFFFFF 0%, #FDFBF8 100%);
                    border-radius: 32px;
                    padding: 28px 28px;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.04);
                    border: 1px solid #3A342D;
                ">
                    <h2 style="
                        font-size: 24px;
                        font-weight: 600;
                        color: #2F2A27;
                        font-family: 'Playfair Display', serif;
                        margin-bottom: 16px;
                    ">
                        Priority Meaning
                    </h2>

                    <div style="height:1px; background: linear-gradient(to right, #E8D9C5, #F3ECE2, transparent); margin-bottom: 18px;"></div>

                    <div style="display:grid; gap:14px;">
                        <div style="color:#6E665C; font-size:14px; line-height:1.7;">
                            <strong style="color:#A37222;">Normal</strong> — general updates for all residents.
                        </div>
                        <div style="color:#6E665C; font-size:14px; line-height:1.7;">
                            <strong style="color:#B8842F;">Important</strong> — matters that should be reviewed soon.
                        </div>
                        <div style="color:#6E665C; font-size:14px; line-height:1.7;">
                            <strong style="color:#B96A5D;">Urgent</strong> — time-sensitive notice requiring immediate awareness.
                        </div>
                    </div>
                </div>

                <div style="
                    background: linear-gradient(180deg, #FFFFFF 0%, #FDFBF8 100%);
                    border-radius: 32px;
                    padding: 28px 28px;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.04);
                    border: 1px solid #3A342D;
                ">
                    <h2 style="
                        font-size: 24px;
                        font-weight: 600;
                        color: #2F2A27;
                        font-family: 'Playfair Display', serif;
                        margin-bottom: 16px;
                    ">
                        Quick Actions
                    </h2>

                    <div style="height:1px; background: linear-gradient(to right, #E8D9C5, #F3ECE2, transparent); margin-bottom: 18px;"></div>

                    <div class="flex flex-col gap-3">
                        <a href="{{ route('announcements.index') }}"
                           style="text-decoration:none; color:#BE9360; font-weight:700; font-size:14px;">
                            View all announcements →
                        </a>

                        @if(auth()->user()->role == 'manager')
                            <a href="{{ route('announcements.create') }}"
                               style="text-decoration:none; color:#BE9360; font-weight:700; font-size:14px;">
                                Post another announcement →
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>