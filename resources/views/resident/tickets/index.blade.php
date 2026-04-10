<x-app-layout>
    @php
        $openTickets = $tickets->whereIn('status', ['received', 'assigned', 'in_progress'])->count();
        $resolvedTickets = $tickets->where('status', 'completed')->count();
        $urgentTickets = $tickets->whereIn('priority', ['high', 'urgent'])->count();
    @endphp
    <div class="resident-page">
        <section class="resident-page-hero">
            <div class="resident-page-hero-copy">
                <p class="resident-page-kicker">Resident Maintenance Hub</p>
                <h1 class="resident-page-title">My Maintenance Tickets</h1>
                <p class="resident-page-subtitle">
                    Review every submitted concern, monitor progress updates, and keep your maintenance history in one clear timeline.
                </p>

                <div class="resident-hero-stat-row">
                    <div class="resident-hero-stat">
                        <span>Open</span>
                        <strong>{{ $openTickets }}</strong>
                    </div>
                    <div class="resident-hero-stat">
                        <span>Resolved</span>
                        <strong>{{ $resolvedTickets }}</strong>
                    </div>
                    <div class="resident-hero-stat">
                        <span>Priority</span>
                        <strong>{{ $urgentTickets }}</strong>
                    </div>
                </div>
            </div>

            <div class="resident-page-actions">
                <a href="{{ route('tickets.create') }}" class="resident-page-btn resident-page-btn-primary">Create New Ticket</a>
            </div>
        </section>

        @if(session('success'))
            <div class="resident-flash resident-flash-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="resident-flash resident-flash-error">{{ session('error') }}</div>
        @endif

        <section class="resident-page-panel">
            <div class="resident-page-panel-head">
                <div>
                    <h2>Submitted Tickets</h2>
                    <p>Your maintenance requests and their latest status.</p>
                </div>
                <span class="resident-page-eyebrow">Concern History</span>
            </div>

            <div class="resident-page-divider"></div>

            <div class="resident-page-list">
                @forelse($tickets as $ticket)
                    <article class="resident-card">
                        <div class="resident-card-top">
                            <div>
                                <div class="resident-card-heading">
                                    <h3>{{ $ticket->title }}</h3>
                                    <span class="resident-badge resident-badge-status-{{ $ticket->status }}">
                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                    <span class="resident-badge resident-badge-priority-{{ $ticket->priority }}">
                                        {{ ucfirst($ticket->priority) }} Priority
                                    </span>
                                </div>
                                <p class="resident-card-description">{{ Str::limit($ticket->description, 160) }}</p>
                            </div>

                            <div class="resident-card-links">
                                @if(in_array($ticket->status, ['received', 'assigned']))
                                    <a href="{{ route('tickets.edit', $ticket) }}">Edit</a>
                                @endif
                                <a href="{{ route('tickets.show', $ticket) }}">View Details</a>
                            </div>
                        </div>

                        @if($ticket->image_path || $ticket->video_path)
                            <div class="resident-media-row">
                                @if($ticket->image_path)
                                    <a href="{{ asset('storage/' . $ticket->image_path) }}" target="_blank" class="resident-media-chip">
                                        Photo Attachment
                                    </a>
                                @endif

                                @if($ticket->video_path)
                                    <a href="{{ asset('storage/' . $ticket->video_path) }}" target="_blank" class="resident-media-chip">
                                        Video Attachment
                                    </a>
                                @endif
                            </div>
                        @endif

                        <div class="resident-card-meta-grid">
                            <div class="resident-meta-box">
                                <span>Ticket ID</span>
                                <strong>{{ $ticket->ticket_id }}</strong>
                            </div>
                            <div class="resident-meta-box">
                                <span>Submitted</span>
                                <strong>{{ $ticket->created_at->format('M d, Y h:i A') }}</strong>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="resident-empty-state">
                        <h3>No tickets found</h3>
                        <p>Create your first maintenance request to get started.</p>
                        <a href="{{ route('tickets.create') }}">Create Your First Ticket</a>
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <style>
        .resident-page {
            max-width: 1600px;
            margin: 0 auto;
            padding: 24px 16px 32px;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .resident-page-hero,
        .resident-page-panel,
        .resident-flash {
            border: 1px solid rgba(214,168,91,0.14);
            box-shadow: 0 12px 24px rgba(0,0,0,0.14);
        }

        .resident-page-hero {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
            padding: 28px 30px;
            border-radius: 36px;
            background: linear-gradient(115deg, #1F2023 0%, #24262B 38%, #2C2C2F 62%, #3B3023 100%);
            box-shadow: 0 18px 50px rgba(0, 0, 0, 0.18);
        }

        .resident-page-hero-copy {
            max-width: 860px;
        }

        .resident-page-kicker {
            margin: 0 0 10px;
            color: #D2A04C;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.30em;
        }

        .resident-page-title {
            margin: 0;
            color: #F8F3EA;
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.4rem, 4.6vw, 3.8rem);
            line-height: 1.05;
        }

        .resident-page-subtitle {
            margin: 12px 0 0;
            color: rgba(255,255,255,0.82);
            font-size: 1.02rem;
            line-height: 1.7;
            max-width: 760px;
        }

        .resident-hero-stat-row {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 22px;
        }

        .resident-hero-stat {
            min-width: 110px;
            padding: 12px 14px;
            border-radius: 16px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.07);
        }

        .resident-hero-stat span {
            display: block;
            color: #A89376;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            font-weight: 700;
        }

        .resident-hero-stat strong {
            display: block;
            margin-top: 6px;
            color: #F0E9DF;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .resident-page-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .resident-page-btn {
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

        .resident-page-btn:hover {
            transform: translateY(-1px);
        }

        .resident-page-btn-primary {
            background: linear-gradient(95deg, #b8842f, #d6a85b);
            color: #17120d;
        }

        .resident-flash {
            padding: 16px 20px;
            border-radius: 20px;
            font-size: 0.95rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }

        .resident-flash-success {
            background: rgba(42,44,48,0.78);
            color: #F0E9DF;
        }

        .resident-flash-error {
            background: linear-gradient(180deg, rgba(53, 38, 35, 0.92) 0%, rgba(42, 31, 29, 0.92) 100%);
            color: #F0B3A9;
            border-color: rgba(224,112,96,0.22);
        }

        .resident-page-panel {
            padding: 26px 28px;
            border-radius: 20px;
            background: rgba(42,44,48,0.78);
            backdrop-filter: blur(10px);
        }

        .resident-page-panel-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 16px;
        }

        .resident-page-panel-head h2 {
            margin: 0;
            color: #F0E9DF;
            font-size: 1.5rem;
            font-family: 'Playfair Display', serif;
        }

        .resident-page-panel-head p {
            margin: 4px 0 0;
            color: #8A7A66;
            font-size: 0.95rem;
        }

        .resident-page-eyebrow {
            color: #D6A85B;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.16em;
        }

        .resident-page-divider {
            height: 1px;
            background: linear-gradient(to right, rgba(214,168,91,0.3), rgba(214,168,91,0.05), transparent);
            margin-bottom: 18px;
        }

        .resident-page-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .resident-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 22px;
            transition: transform 0.18s ease, border-color 0.18s ease;
        }

        .resident-card:hover {
            transform: translateY(-2px);
            border-color: rgba(214,168,91,0.18);
        }

        .resident-card-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 18px;
        }

        .resident-card-heading {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
        }

        .resident-card-heading h3 {
            margin: 0;
            color: #f0e9df;
            font-size: 1.05rem;
            font-weight: 700;
        }

        .resident-card-description {
            margin: 12px 0 0;
            color: #B8AB98;
            line-height: 1.65;
            font-size: 0.9rem;
        }

        .resident-card-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 14px;
        }

        .resident-card-links a,
        .resident-empty-state a {
            color: #d7b07a;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .resident-badge {
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 0.68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .resident-badge-status-received,
        .resident-badge-status-pending {
            background: rgba(199, 151, 69, 0.18);
            color: #d7b07a;
        }

        .resident-badge-status-assigned,
        .resident-badge-status-in_progress {
            background: rgba(183, 147, 93, 0.22);
            color: #f0c37b;
        }

        .resident-badge-status-completed {
            background: rgba(255, 255, 255, 0.08);
            color: #beb1a0;
        }

        .resident-badge-priority-low {
            background: rgba(120, 170, 120, 0.16);
            color: #98c48b;
        }

        .resident-badge-priority-medium {
            background: rgba(199, 151, 69, 0.18);
            color: #d7b07a;
        }

        .resident-badge-priority-high {
            background: rgba(185, 106, 93, 0.16);
            color: #dc9a86;
        }

        .resident-media-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 16px;
        }

        .resident-media-chip {
            padding: 9px 14px;
            border-radius: 999px;
            border: 1px solid rgba(214, 168, 91, 0.3);
            color: #e4d1b0;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.03);
        }

        .resident-card-meta-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 18px;
        }

        .resident-meta-box {
            padding: 12px 14px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255,255,255,0.05);
        }

        .resident-meta-box span {
            display: block;
            color: #8A7A66;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .resident-meta-box strong {
            display: block;
            margin-top: 6px;
            color: #f0e9df;
            font-size: 0.86rem;
            font-weight: 600;
        }

        .resident-empty-state {
            padding: 34px 20px;
            border-radius: 16px;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.05);
            background: rgba(255, 255, 255, 0.03);
        }

        .resident-empty-state h3 {
            margin: 0;
            color: #f0e9df;
            font-size: 1.3rem;
            font-family: 'Playfair Display', serif;
        }

        .resident-empty-state p {
            margin: 10px 0 18px;
            color: #B8AB98;
            font-size: 0.92rem;
        }

        @media (max-width: 768px) {
            .resident-page {
                padding: 18px 0 28px;
            }

            .resident-page-hero,
            .resident-page-panel {
                padding: 22px;
            }

            .resident-page-hero,
            .resident-card-top {
                flex-direction: column;
                align-items: flex-start;
            }

            .resident-card-links {
                justify-content: flex-start;
            }

            .resident-card-meta-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</x-app-layout>
