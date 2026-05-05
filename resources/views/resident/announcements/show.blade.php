<x-app-layout>
    @php
        $priorityMeta = [
            'normal' => [
                'label' => 'Normal',
                'tone' => 'Routine community notice',
                'badge' => 'rgba(214,168,91,0.12)',
                'border' => 'rgba(214,168,91,0.18)',
                'text' => '#E9D8BD',
                'panel' => 'rgba(214,168,91,0.06)',
            ],
            'important' => [
                'label' => 'Important',
                'tone' => 'Needs attention soon',
                'badge' => 'rgba(190,147,96,0.14)',
                'border' => 'rgba(190,147,96,0.22)',
                'text' => '#E4C58E',
                'panel' => 'rgba(190,147,96,0.08)',
            ],
            'urgent' => [
                'label' => 'Urgent',
                'tone' => 'Time-sensitive announcement',
                'badge' => 'rgba(224,112,96,0.14)',
                'border' => 'rgba(224,112,96,0.22)',
                'text' => '#F0B3A9',
                'panel' => 'rgba(224,112,96,0.08)',
            ],
        ];

        $priority = $priorityMeta[$announcement->priority] ?? $priorityMeta['normal'];
    @endphp

    <div class="resident-announcement-page">
        <section class="resident-announcement-hero">
            <div class="resident-announcement-hero-copy">
                <p class="resident-announcement-kicker">Community Notice Details</p>
                <h1 class="resident-announcement-title">Announcement Details</h1>
                <p class="resident-announcement-subtitle">
                    Read the full notice and review the most important context without unnecessary side panels.
                </p>

                <div class="resident-announcement-stat-row">
                    <div class="resident-announcement-stat">
                        <span>Priority</span>
                        <strong>{{ $priority['label'] }}</strong>
                    </div>
                    <div class="resident-announcement-stat">
                        <span>Posted</span>
                        <strong>{{ $announcement->created_at->diffForHumans() }}</strong>
                    </div>
                    <div class="resident-announcement-stat">
                        <span>Date</span>
                        <strong>{{ $announcement->created_at->format('M d, Y') }}</strong>
                    </div>
                </div>
            </div>

            <div class="resident-announcement-hero-actions">
                <a href="{{ route('announcements.index') }}" class="resident-announcement-btn resident-announcement-btn-secondary">Back to Announcements</a>
            </div>
        </section>

        <section class="resident-announcement-panel">
            <div class="resident-announcement-panel-head">
                <div>
                    <h2>{{ $announcement->title }}</h2>
                    <p>Resident-facing announcement content and context.</p>
                </div>
                <span class="resident-announcement-badge" style="background: {{ $priority['badge'] }}; border-color: {{ $priority['border'] }}; color: {{ $priority['text'] }};">
                    {{ $priority['label'] }}
                </span>
            </div>

            <div class="resident-announcement-divider"></div>

            <div class="resident-announcement-highlight" style="border-color: {{ $priority['border'] }};">
                <span>Notice Level</span>
                <strong>{{ $priority['tone'] }}</strong>
            </div>

            <div class="resident-announcement-summary-grid">
                <div class="resident-announcement-meta-item">
                    <span>Date Posted</span>
                    <strong>{{ $announcement->created_at->format('F d, Y h:i A') }}</strong>
                </div>
                <div class="resident-announcement-meta-item">
                    <span>Priority</span>
                    <strong>{{ $priority['label'] }}</strong>
                </div>
            </div>

            <article class="resident-announcement-body">
                {!! nl2br(e($announcement->content)) !!}
            </article>

            <div class="resident-announcement-actions">
                <a href="{{ route('concerns.create', [
                    'category' => 'other',
                    'details' => "Follow-up request regarding announcement: {$announcement->title}\n\nPlease help with:",
                    'context_title' => $announcement->title,
                    'context_type' => 'announcement',
                ]) }}" class="resident-announcement-btn resident-announcement-btn-secondary">Request Follow-up</a>
                <a href="{{ route('announcements.index') }}" class="resident-announcement-btn resident-announcement-btn-primary">View All Announcements</a>
            </div>
        </section>
    </div>

    <style>
        .resident-announcement-page {
            max-width: 1600px;
            margin: 0 auto;
            padding: 24px 16px 32px;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .resident-announcement-hero,
        .resident-announcement-panel {
            border: 1px solid rgba(214,168,91,0.14);
            box-shadow: 0 12px 24px rgba(0,0,0,0.14);
        }

        .resident-announcement-hero {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
            padding: 30px;
            border-radius: 36px;
            background: linear-gradient(115deg, #1F2023 0%, #24262B 38%, #2C2C2F 62%, #3B3023 100%);
            box-shadow: 0 18px 50px rgba(0, 0, 0, 0.18);
        }

        .resident-announcement-hero-copy {
            max-width: 860px;
        }

        .resident-announcement-hero-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 12px;
        }

        .resident-announcement-kicker {
            margin: 0 0 10px;
            color: #D2A04C;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.30em;
        }

        .resident-announcement-title {
            margin: 0;
            color: #F8F3EA;
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.4rem, 4.6vw, 3.8rem);
            line-height: 1.05;
        }

        .resident-announcement-subtitle {
            margin: 12px 0 0;
            color: rgba(255,255,255,0.82);
            font-size: 1.02rem;
            line-height: 1.7;
            max-width: 760px;
        }

        .resident-announcement-stat-row {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 22px;
        }

        .resident-announcement-stat {
            min-width: 130px;
            padding: 12px 14px;
            border-radius: 16px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.07);
        }

        .resident-announcement-stat span {
            display: block;
            color: #A89376;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            font-weight: 700;
        }

        .resident-announcement-stat strong {
            display: block;
            margin-top: 6px;
            color: #F0E9DF;
            font-size: 1.05rem;
            font-weight: 700;
        }

        .resident-announcement-panel {
            padding: 26px 28px;
            border-radius: 20px;
            background: rgba(42,44,48,0.78);
            backdrop-filter: blur(10px);
        }

        .resident-announcement-panel-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 16px;
        }

        .resident-announcement-panel-head h2 {
            margin: 0;
            color: #F0E9DF;
            font-size: 1.5rem;
            font-family: 'Playfair Display', serif;
        }

        .resident-announcement-panel-head p {
            margin: 4px 0 0;
            color: #8A7A66;
            font-size: 0.95rem;
        }

        .resident-announcement-badge {
            padding: 6px 11px;
            border-radius: 999px;
            border: 1px solid transparent;
            font-size: 0.68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            white-space: nowrap;
        }

        .resident-announcement-divider {
            height: 1px;
            background: linear-gradient(to right, rgba(214,168,91,0.3), rgba(214,168,91,0.05), transparent);
            margin-bottom: 18px;
        }

        .resident-announcement-highlight,
        .resident-announcement-meta-item {
            border-radius: 16px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
        }

        .resident-announcement-highlight {
            padding: 16px 18px;
            margin-bottom: 18px;
            background: rgba(255,255,255,0.03);
        }

        .resident-announcement-highlight span,
        .resident-announcement-meta-item span {
            display: block;
            color: #A89376;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            font-weight: 700;
        }

        .resident-announcement-highlight strong,
        .resident-announcement-meta-item strong {
            display: block;
            margin-top: 6px;
            color: #F0E9DF;
            font-size: 1rem;
            font-weight: 700;
        }

        .resident-announcement-summary-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        .resident-announcement-meta-item {
            padding: 16px 18px;
        }

        .resident-announcement-body {
            padding: 24px;
            border-radius: 18px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
            color: #C4B8A8;
            font-size: 0.98rem;
            line-height: 1.9;
        }

        .resident-announcement-actions {
            margin-top: 18px;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .resident-announcement-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 22px;
            border-radius: 999px;
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 700;
            transition: transform 0.2s ease;
        }

        .resident-announcement-btn:hover {
            transform: translateY(-1px);
        }

        .resident-announcement-btn-primary {
            background: linear-gradient(95deg, #b8842f, #d6a85b);
            color: #17120d;
        }

        .resident-announcement-btn-secondary {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(214,168,91,0.22);
            color: #F0E9DF;
        }

        @media (max-width: 768px) {
            .resident-announcement-page {
                padding: 18px 0 28px;
            }

            .resident-announcement-hero,
            .resident-announcement-panel {
                padding: 22px;
            }

            .resident-announcement-hero {
                flex-direction: column;
                align-items: flex-start;
            }

            .resident-announcement-summary-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 560px) {
            .resident-announcement-title {
                font-size: 2.1rem;
            }

            .resident-announcement-hero-actions,
            .resident-announcement-btn {
                width: 100%;
            }
        }
    </style>
</x-app-layout>
