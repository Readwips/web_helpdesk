<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetRepair;
use App\Models\KnowledgeArticle;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_admin_menu_destination_renders_without_error(): void
    {
        $this->seed();
        $admin = User::where('role', 'admin')->firstOrFail();
        $routes = [
            route('dashboard'), route('tickets.index'), route('tickets.create'),
            route('assets.index'), route('assets.create'), route('knowledge.index'),
            route('knowledge.create'), route('users.index'), route('users.create'),
            route('departments.index'), route('ticket-categories.index'),
            route('asset-categories.index'), route('reports.index'), route('profile.edit'),
        ];
        foreach ($routes as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_seeded_detail_pages_render_without_error(): void
    {
        $this->seed();
        $admin = User::where('role', 'admin')->firstOrFail();
        $urls = [
            route('tickets.show', Ticket::firstOrFail()),
            route('assets.show', Asset::firstOrFail()),
            route('knowledge.show', KnowledgeArticle::firstOrFail()),
            route('repairs.show', AssetRepair::firstOrFail()),
        ];
        foreach ($urls as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }
}
