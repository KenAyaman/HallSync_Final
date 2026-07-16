<?php

namespace Tests\Feature;

use App\Models\MaintenanceTicket;
use App\Models\Concern;
use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_dashboard_displays_predictive_and_prescriptive_analytics(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $resident = User::factory()->create(['role' => 'resident']);

        MaintenanceTicket::create([
            'user_id' => $resident->id,
            'ticket_id' => 'TKT-ANALYTICS-1',
            'title' => 'Urgent electrical issue',
            'description' => 'The lights are flickering.',
            'category' => 'electrical',
            'priority' => 'urgent',
            'status' => 'pending_approval',
        ]);

        $agingTicket = MaintenanceTicket::create([
            'user_id' => $resident->id,
            'ticket_id' => 'TKT-ANALYTICS-2',
            'title' => 'Aging plumbing issue',
            'description' => 'The sink is leaking.',
            'category' => 'plumbing',
            'priority' => 'medium',
            'status' => 'approved',
        ]);
        $agingTicket->forceFill(['created_at' => now()->subDays(3)])->saveQuietly();

        Concern::create([
            'user_id' => $resident->id,
            'category' => 'facility',
            'subject' => 'Noise concern',
            'details' => 'Please review the recurring noise near the study area.',
            'status' => 'submitted',
        ]);

        $this->actingAs($manager)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Operations Intelligence')
            ->assertSee('Unassigned Workload')
            ->assertSee('Resident Concerns')
            ->assertSee('Available Staff')
            ->assertSee('Category Hotspot')
            ->assertSee('Descriptive Analytics')
            ->assertSee('Predictive Operations')
            ->assertSee('Ticket Demand Forecast')
            ->assertSee('Recommended Actions')
            ->assertSee('Assign critical maintenance requests')
            ->assertSee('Review aging open tickets')
            ->assertSee('Respond to unresolved private concerns');
    }

    public function test_manager_dashboard_displays_operational_audit_events(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        UserActivityLog::recordDomain('booking.cancelled', 'Cancelled a facility booking and released its slot.', $manager);

        $this->actingAs($manager)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Operational audit trail')
            ->assertSee('Cancelled a facility booking and released its slot.')
            ->assertSee('booking.cancelled');
    }

    public function test_manager_dashboard_limits_activity_log_rows_before_expansion(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);

        $this->actingAs($manager)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('data-progressive-limit="3"', false)
            ->assertSee('data-progressive-limit="5"', false);
    }

}
