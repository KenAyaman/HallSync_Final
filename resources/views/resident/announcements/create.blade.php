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
                                Manager Notice Board
                            </span>
                        </div>

                        <h1 class="text-4xl md:text-5xl font-bold leading-[1.05] mb-4"
                            style="font-family: 'Playfair Display', serif; color: #F8F3EA;">
                            Post New<br>
                            <span style="color: #F3E5CF;">Announcement</span>
                        </h1>

                        <p class="text-base md:text-lg leading-relaxed max-w-xl"
                           style="color: rgba(255,255,255,0.82);">
                            Share important updates, reminders, and announcements
                            with residents in a clear and organized way.
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

        <div class="grid lg:grid-cols-[1.2fr_0.8fr] gap-8">

            {{-- FORM --}}
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
                        Announcement Details
                    </h2>

                    <span style="
                        font-size: 12px;
                        letter-spacing: 0.18em;
                        text-transform: uppercase;
                        color: #BE9360;
                        font-weight: 700;
                    ">
                        Publish Notice
                    </span>
                </div>

                <div style="height:1px; background: linear-gradient(to right, #E8D9C5, #F3ECE2, transparent); margin-bottom: 24px;"></div>

                <form method="POST" action="{{ route('announcements.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label style="display:block; font-weight:700; margin-bottom:10px; color:#2F2A27; font-size:14px;">
                            Title <span style="color:#B96A5D;">*</span>
                        </label>
                        <input type="text" name="title" required
                               style="
                                    width:100%;
                                    padding:14px 16px;
                                    border:1px solid #D9CCBA;
                                    border-radius:16px;
                                    font-size:15px;
                                    color:#2F2A27;
                                    background:#FFFEFC;
                                    outline:none;
                               ">
                    </div>

                    <div>
                        <label style="display:block; font-weight:700; margin-bottom:10px; color:#2F2A27; font-size:14px;">
                            Content <span style="color:#B96A5D;">*</span>
                        </label>
                        <textarea name="content" rows="6" required
                                  style="
                                        width:100%;
                                        padding:14px 16px;
                                        border:1px solid #D9CCBA;
                                        border-radius:16px;
                                        font-size:15px;
                                        color:#2F2A27;
                                        background:#FFFEFC;
                                        outline:none;
                                        resize:vertical;
                                  "></textarea>
                    </div>

                    <div>
                        <label style="display:block; font-weight:700; margin-bottom:10px; color:#2F2A27; font-size:14px;">
                            Priority <span style="color:#B96A5D;">*</span>
                        </label>
                        <select name="priority"
                                style="
                                    width:100%;
                                    padding:14px 16px;
                                    border:1px solid #D9CCBA;
                                    border-radius:16px;
                                    font-size:15px;
                                    color:#2F2A27;
                                    background:#FFFEFC;
                                    outline:none;
                                ">
                            <option value="normal">Normal</option>
                            <option value="important">Important</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>

                    <div style="padding-top: 8px; border-top: 1px solid #EFE6DA;">
                        <button type="submit"
                                style="
                                    background: linear-gradient(90deg, #B8842F 0%, #D6A85B 100%);
                                    color: #FFFFFF;
                                    padding: 14px 28px;
                                    border-radius: 999px;
                                    font-weight: 700;
                                    border: none;
                                    cursor: pointer;
                                    box-shadow: 0 10px 24px rgba(199, 150, 69, 0.28);
                                    transition: all 0.3s ease;
                                    font-size: 14px;
                                "
                                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 16px 30px rgba(199,150,69,0.34)';"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 24px rgba(199,150,69,0.28)';">
                            Publish Announcement
                        </button>
                    </div>
                </form>
            </div>

            {{-- SIDE --}}
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
                        Priority Guide
                    </h2>

                    <div style="height:1px; background: linear-gradient(to right, #E8D9C5, #F3ECE2, transparent); margin-bottom: 18px;"></div>

                    <div style="display:grid; gap:14px; color:#6E665C; font-size:14px; line-height:1.7;">
                        <div><strong style="color:#A37222;">Normal</strong> — routine updates for residents.</div>
                        <div><strong style="color:#B8842F;">Important</strong> — announcements that need attention soon.</div>
                        <div><strong style="color:#B96A5D;">Urgent</strong> — immediate or critical community notice.</div>
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
                        Writing Tips
                    </h2>

                    <div style="height:1px; background: linear-gradient(to right, #E8D9C5, #F3ECE2, transparent); margin-bottom: 18px;"></div>

                    <div style="color:#6E665C; font-size:14px; line-height:1.8;">
                        <div>• Keep the title short and clear.</div>
                        <div>• Put the most important details first.</div>
                        <div>• Use urgent only for time-sensitive matters.</div>
                        <div>• Make instructions easy for residents to follow.</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>