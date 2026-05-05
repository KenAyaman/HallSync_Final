<x-app-layout>
<div class="admin-shell admin-announce-edit-page">
    {{-- HERO --}}
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
                            Announcement Center
                        </span>
                    </div>

                    <h1 class="text-4xl md:text-5xl font-bold leading-[1.05] mb-4"
                        style="font-family: 'Playfair Display', serif; color: #F8F3EA;">
                        Edit<br>
                        <span style="color: #F3E5CF;">Announcement</span>
                    </h1>

                    <p class="text-base md:text-lg leading-relaxed max-w-xl"
                       style="color: rgba(255,255,255,0.82);">
                        Create important updates, set priority levels for urgent notices, and keep residents informed about facility changes and events.
                    </p>
                </div>

                <div class="shrink-0">
                    <a href="{{ route('announcements.index') }}"
                       style="
                            background: linear-gradient(90deg, #B8842F 0%, #D6A85B 100%);
                            color: #FFFFFF;
                            padding: 15px 32px;
                            border-radius: 999px;
                            font-weight: 700;
                            text-decoration: none;
                            box-shadow: 0 12px 28px rgba(199, 150, 69, 0.3);
                            transition: all 0.3s ease;
                            display: inline-block;
                        "
                       onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 20px 40px rgba(199,150,69,0.4)';"
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 12px 28px rgba(199,150,69,0.3)';">
                        Back to Announcements
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ERROR BOX --}}
    @if ($errors->any())
        <div class="admin-error-box">
            <div class="admin-error-title">Please fix the following:</div>
            <ul class="admin-error-list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- MAIN CONTENT --}}
    <div class="admin-panel-card admin-form-panel">
            <div class="admin-panel-head">
                <div>
                    <h2 class="admin-panel-title">Announcement Details</h2>
                    <p class="admin-panel-sub">Update the message before publishing it to residents.</p>
                </div>

                <span class="admin-panel-badge">
                    Update Notice
                </span>
            </div>

            <div class="admin-panel-divider"></div>

            <form method="POST" action="{{ route('announcements.update', $announcement) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="admin-label">Title</label>
                    <input
                        type="text"
                        name="title"
                        value="{{ old('title', $announcement->title) }}"
                        required
                        class="admin-input">
                </div>

                <div>
                    <label class="admin-label">Content</label>
                    <textarea
                        name="content"
                        rows="8"
                        required
                        class="admin-input admin-textarea">{{ old('content', $announcement->content) }}</textarea>
                </div>

                <div>
                    <label class="admin-label">Priority</label>
                    <select name="priority" class="admin-input admin-select">
                        <option value="normal" @selected(old('priority', $announcement->priority) === 'normal')>Normal</option>
                        <option value="important" @selected(old('priority', $announcement->priority) === 'important')>Important</option>
                        <option value="urgent" @selected(old('priority', $announcement->priority) === 'urgent')>Urgent</option>
                    </select>
                </div>

                <div class="admin-form-actions">
                    <button type="submit" class="admin-primary-btn">
                        Save Changes
                    </button>

                    <a href="{{ route('announcements.index') }}" class="admin-secondary-btn">
                        Cancel
                    </a>
                </div>
            </form>
    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap');

.admin-shell.admin-announce-edit-page {
    font-family: 'Inter', sans-serif;
    color: #c4b8a8;
    min-height: 100vh;
    padding: 0;
    max-width: 1580px;
    width: 100%;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 28px;
    position: relative;
    z-index: 1;
    font-size: 16px;
    line-height: 1.55;
}

.admin-error-box {
    background: linear-gradient(180deg, rgba(53, 38, 35, 0.92) 0%, rgba(42, 31, 29, 0.92) 100%);
    border: 1px solid rgba(224,112,96,0.22);
    border-radius: 20px;
    padding: 18px 22px;
    color: #F0B3A9;
    box-shadow: 0 16px 32px rgba(0,0,0,0.18);
}

.admin-error-title {
    font-weight: 700;
    margin-bottom: 8px;
    color: #FFB2A7;
}

.admin-error-list {
    margin: 0;
    padding-left: 18px;
    color: #E7C3BD;
    line-height: 1.7;
}

.admin-panel-card {
    background: rgba(42,44,48,0.78);
    border-radius: 20px;
    padding: 24px;
    border: 1px solid rgba(214,168,91,0.14);
    backdrop-filter: blur(10px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.14);
}

.admin-form-panel {
    padding: 26px 28px;
}

.admin-panel-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}

.admin-panel-title {
    margin: 0;
    color: #F0E9DF;
    font-size: 1.5rem;
    font-weight: 600;
    font-family: 'Playfair Display', serif;
}

.admin-panel-sub {
    margin-top: 4px;
    color: #8A7A66;
    font-size: 0.95rem;
}

.admin-panel-badge {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: #D6A85B;
    padding: 8px 12px;
    border-radius: 999px;
    background: rgba(214,168,91,0.10);
    border: 1px solid rgba(214,168,91,0.16);
}

.admin-panel-divider {
    height: 1px;
    background: linear-gradient(to right, rgba(214,168,91,0.3), rgba(214,168,91,0.05), transparent);
    margin-bottom: 24px;
}

.admin-label {
    display: block;
    font-weight: 700;
    margin-bottom: 10px;
    color: #D0C8B8;
    font-size: 14px;
    letter-spacing: 0.02em;
}

.admin-input {
    width: 100%;
    padding: 14px 16px;
    border: 1px solid rgba(214,168,91,0.14);
    border-radius: 16px;
    font-size: 15px;
    color: #F8F3EA;
    background: rgba(37,39,42,0.90);
    outline: none;
    transition: all 0.2s ease;
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.02);
}

.admin-input::placeholder {
    color: #8A7A66;
}

.admin-input:focus {
    border-color: rgba(214,168,91,0.38);
    box-shadow: 0 0 0 4px rgba(214,168,91,0.08);
}

.admin-textarea {
    resize: vertical;
    min-height: 180px;
    line-height: 1.7;
}

.admin-select {
    appearance: none;
}

.admin-form-actions {
    padding-top: 8px;
    border-top: 1px solid rgba(214,168,91,0.10);
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.admin-primary-btn {
    background: linear-gradient(90deg, #B8842F 0%, #D6A85B 100%);
    color: #FFFFFF;
    padding: 14px 28px;
    border-radius: 999px;
    font-weight: 700;
    border: none;
    cursor: pointer;
    box-shadow: 0 10px 24px rgba(199, 150, 69, 0.28);
    font-size: 14px;
    transition: all 0.2s ease;
}

.admin-primary-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 14px 28px rgba(199, 150, 69, 0.34);
}

.admin-secondary-btn {
    background: rgba(255,255,255,0.04);
    color: #D0C8B8;
    padding: 14px 22px;
    border-radius: 999px;
    font-weight: 600;
    text-decoration: none;
    border: 1px solid rgba(214,168,91,0.14);
    display: inline-flex;
    align-items: center;
    transition: all 0.2s ease;
}

.admin-secondary-btn:hover {
    background: rgba(255,255,255,0.08);
    color: #F0E9DF;
}

/* Scrollbar */
::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
::-webkit-scrollbar-track {
    background: #2A2C30;
    border-radius: 10px;
}
::-webkit-scrollbar-thumb {
    background: #D6A85B;
    border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
    background: #C49A4A;
}

@media (max-width: 768px) {
    .admin-shell.admin-announce-edit-page {
        gap: 18px;
    }

    .admin-form-panel,
    .admin-panel-card {
        padding: 20px;
    }

    .admin-panel-title {
        font-size: 1.3rem;
    }
}

@media (max-width: 560px) {
    .admin-primary-btn,
    .admin-secondary-btn,
    .admin-shell.admin-announce-edit-page > div:first-of-type a {
        width: 100%;
        justify-content: center;
    }

    .admin-form-actions {
        flex-direction: column;
    }
}
</style>
</x-app-layout>
