<x-app-layout>
@php
    $expirationOption = old('expiration_option', $announcement->is_pinned
        ? 'never'
        : ($announcement->expires_at ? 'custom' : '7_days'));
@endphp
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

                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-[1.02] mb-6"
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
                    <a href="{{ route('announcements.index') }}" class="admin-hero-action admin-hero-action-secondary"
                       style="
                            background: linear-gradient(90deg, #B8842F 0%, #D6A85B 100%);
                            color: #FFFFFF;
                            padding: 0px 20px;
                            border-radius: 777px;
                            font-weight: 800;
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

            <form method="POST"
                  action="{{ route('announcements.update', $announcement) }}"
                  class="space-y-6"
                  data-prevent-double-submit
                  data-submitting-text="Saving Announcement...">
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
                    <select name="priority" class="admin-input admin-select" required>
                        <option value="normal" @selected(old('priority', $announcement->priority) === 'normal')>Normal</option>
                        <option value="important" @selected(old('priority', $announcement->priority) === 'important')>Important</option>
                        <option value="urgent" @selected(old('priority', $announcement->priority) === 'urgent')>Urgent</option>
                    </select>
                </div>

                @php
                    $existingStartDate = optional($announcement->starts_at)->format('Y-m-d');
                    $existingStartTime = optional($announcement->starts_at)->format('H:i');
                    $existingExpiresDate = optional($announcement->expires_at)->format('Y-m-d');
                    $existingExpiresTime = optional($announcement->expires_at)->format('H:i');
                @endphp

                <div>
                    <label class="admin-label">Start Date / Time</label>
                    <div class="admin-datetime-row">
                        <input type="date" class="admin-input admin-datetime-date" data-dt-date="starts_at" value="{{ old('starts_at_date', $existingStartDate) }}" min="{{ now()->toDateString() }}">
                        <select class="admin-input admin-select admin-datetime-time" data-dt-time="starts_at">
                            <option value="">— Time —</option>
                            @for($h = 0; $h < 24; $h++)
                                @foreach(['00','30'] as $m)
                                    @php $val = sprintf('%02d:%s', $h, $m); $lbl = \Carbon\Carbon::createFromFormat('H:i', $val)->format('g:i A'); @endphp
                                    <option value="{{ $val }}" @selected(old('starts_at_time', $existingStartTime) === $val)>{{ $lbl }}</option>
                                @endforeach
                            @endfor
                        </select>
                    </div>
                    <input type="hidden" name="starts_at" value="{{ old('starts_at', optional($announcement->starts_at)->format('Y-m-d\\TH:i')) }}" data-dt-hidden="starts_at">
                    <p class="admin-panel-sub">Leave blank to publish immediately.</p>
                </div>

                <div>
                    <label class="admin-label">Expiration</label>
                    <select name="expiration_option" class="admin-input admin-select" data-expiration-select required>
                        <option value="7_days" @selected($expirationOption === '7_days')>7 Days</option>
                        <option value="24_hours" @selected($expirationOption === '24_hours')>24 Hours</option>
                        <option value="30_days" @selected($expirationOption === '30_days')>30 Days</option>
                        <option value="custom" @selected($expirationOption === 'custom')>Custom Date...</option>
                        <option value="never" @selected($expirationOption === 'never')>Never Expire (Pin to Top)</option>
                    </select>
                </div>

                <div data-custom-expiration style="display: none;">
                    <label class="admin-label">Custom Expiration Date / Time</label>
                    <div class="admin-datetime-row">
                        <input type="date" class="admin-input admin-datetime-date" data-dt-date="custom_expires_at" value="{{ old('custom_expires_at_date', $existingExpiresDate) }}" min="{{ now()->addDay()->toDateString() }}">
                        <select class="admin-input admin-select admin-datetime-time" data-dt-time="custom_expires_at">
                            <option value="">— Time —</option>
                            @for($h = 0; $h < 24; $h++)
                                @foreach(['00','30'] as $m)
                                    @php $val = sprintf('%02d:%s', $h, $m); $lbl = \Carbon\Carbon::createFromFormat('H:i', $val)->format('g:i A'); @endphp
                                    <option value="{{ $val }}" @selected(old('custom_expires_at_time', $existingExpiresTime) === $val)>{{ $lbl }}</option>
                                @endforeach
                            @endfor
                        </select>
                    </div>
                    <input type="hidden" name="custom_expires_at" value="{{ old('custom_expires_at', optional($announcement->expires_at)->format('Y-m-d\\TH:i')) }}" data-dt-hidden="custom_expires_at">
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

<script>
document.querySelectorAll('[data-expiration-select]').forEach((select) => {
    const customField = select.closest('form').querySelector('[data-custom-expiration]');
    const syncCustomExpiration = () => {
        const custom = select.value === 'custom';
        customField.style.display = custom ? 'block' : 'none';
        customField.querySelector('[data-dt-hidden]').toggleAttribute('required', custom);
    };
    select.addEventListener('change', syncCustomExpiration);
    syncCustomExpiration();
});

document.querySelectorAll('[data-dt-date]').forEach((dateInput) => {
    const key = dateInput.dataset.dtDate;
    const form = dateInput.closest('form');
    const timeSelect = form.querySelector(`[data-dt-time="${key}"]`);
    const hiddenInput = form.querySelector(`[data-dt-hidden="${key}"]`);
    const combine = () => {
        const d = dateInput.value;
        const t = timeSelect.value;
        hiddenInput.value = d && t ? `${d}T${t}` : '';
    };
    dateInput.addEventListener('change', combine);
    timeSelect.addEventListener('change', combine);
});
</script>

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
.admin-panel-card {
    background: rgba(42, 44, 48, 0.78);
    border-radius: 20px;
    padding: 24px;
    border: 1px solid rgba(214, 168, 91, 0.14);
    backdrop-filter: blur(10px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.14);
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
    background: rgba(214, 168, 91, 0.10);
    border: 1px solid rgba(214, 168, 91, 0.16);
}
.admin-panel-divider {
    height: 1px;
    background: linear-gradient(to right, rgba(214, 168, 91, 0.3), rgba(214, 168, 91, 0.05), transparent);
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
    border: 1px solid rgba(214, 168, 91, 0.14);
    border-radius: 16px;
    font-size: 15px;
    color: #F8F3EA;
    background: rgba(37, 39, 42, 0.90);
    outline: none;
    transition: all 0.2s ease;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.02);
}
.admin-input::placeholder {
    color: #8A7A66;
}
.admin-input:focus {
    border-color: rgba(214, 168, 91, 0.38);
    box-shadow: 0 0 0 4px rgba(214, 168, 91, 0.08);
}
.admin-textarea {
    resize: vertical;
    min-height: 180px;
    line-height: 1.7;
}
.admin-select {
    appearance: none;
}
.admin-datetime-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}
.admin-form-actions {
    padding-top: 8px;
    border-top: 1px solid rgba(214, 168, 91, 0.10);
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
    background: rgba(255, 255, 255, 0.04);
    color: #D0C8B8;
    padding: 14px 22px;
    border-radius: 999px;
    font-weight: 600;
    text-decoration: none;
    border: 1px solid rgba(214, 168, 91, 0.14);
    display: inline-flex;
    align-items: center;
    transition: all 0.2s ease;
}
.admin-secondary-btn:hover {
    background: rgba(255, 255, 255, 0.08);
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
@media (max-width:768px) {
    .admin-shell.admin-announce-edit-page {
        gap: 18px;
    }
    .admin-form-panel, .admin-panel-card {
        padding: 20px;
    }
    .admin-panel-title {
        font-size: 1.3rem;
    }
}
@media (max-width:560px) {
    .admin-primary-btn, .admin-secondary-btn, .admin-shell.admin-announce-edit-page > div:first-of-type a {
        width: 100%;
        justify-content: center;
    }
    .admin-form-actions {
        flex-direction: column;
    }
}
</style>
</x-app-layout>
