<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetRepair;
use App\Models\KnowledgeArticle;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $tickets = Ticket::query();
        if ($user->role === 'user') {
            $tickets->where('user_id', $user->id);
        } elseif ($user->role === 'technician') {
            $tickets->where('technician_id', $user->id);
        }
        $base = clone $tickets;
        $stats = [
            'total_tickets' => (clone $base)->count(),
            'new' => (clone $base)->where('status', 'baru')->count(),
            'active' => (clone $base)->whereIn('status', ['ditugaskan', 'diproses'])->count(),
            'waiting' => (clone $base)->where('status', 'menunggu_konfirmasi')->count(),
            'done' => (clone $base)->where('status', 'selesai')->count(),
            'done_month' => (clone $base)->where('status', 'selesai')->whereMonth('confirmed_at', now()->month)->whereYear('confirmed_at', now()->year)->count(),
            'urgent' => (clone $base)->whereIn('priority', ['tinggi', 'kritis'])->where('status', '!=', 'selesai')->count(),
        ];
        $months = collect(range(5, 0))->map(fn ($n) => now()->subMonths($n));
        $chart = [
            'labels' => $months->map->format('M Y'),
            'values' => $months->map(fn ($m) => (clone $base)->whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)->count()),
        ];
        $assetQuery = $user->role === 'user' ? Asset::where('assigned_user_id', $user->id) : Asset::query();
        $assetStats = [
            'total' => (clone $assetQuery)->count(),
            'available' => (clone $assetQuery)->where('status', 'tersedia')->count(),
            'used' => (clone $assetQuery)->whereIn('status', ['digunakan', 'dipinjamkan'])->count(),
            'broken' => (clone $assetQuery)->whereIn('status', ['rusak', 'diperbaiki'])->count(),
        ];
        $adminCharts = null;
        if ($user->isAdmin()) {
            $adminCharts = [
                'status' => $this->counts(Ticket::class, 'status', ['baru', 'ditugaskan', 'diproses', 'menunggu_konfirmasi', 'selesai', 'dibatalkan']),
                'priority' => $this->counts(Ticket::class, 'priority', ['rendah', 'sedang', 'tinggi', 'kritis']),
                'category' => ['labels' => TicketCategory::pluck('name')->values(), 'values' => TicketCategory::withCount('tickets')->pluck('tickets_count')->values()],
                'assetCategory' => ['labels' => AssetCategory::pluck('name')->values(), 'values' => AssetCategory::withCount('assets')->pluck('assets_count')->values()],
                'condition' => $this->counts(Asset::class, 'condition', ['baik', 'perlu_perawatan', 'rusak_ringan', 'rusak_berat']),
                'technician' => ['labels' => User::where('role', 'technician')->pluck('name')->values(), 'values' => User::where('role', 'technician')->withCount(['assignedTickets as completed_count' => fn ($q) => $q->where('status', 'selesai')])->pluck('completed_count')->values()],
            ];
        }

        return view('dashboard', [
            'stats' => $stats,
            'chart' => $chart,
            'adminCharts' => $adminCharts,
            'assetStats' => $assetStats,
            'user' => $user,
            'latestTickets' => (clone $base)->with(['user', 'technician'])->latest()->limit(6)->get(),
            'assets' => (clone $assetQuery)->with('category')->limit(5)->get(),
            'articles' => KnowledgeArticle::where('status', 'published')->latest('published_at')->limit(4)->get(),
            'repairCost' => $user->isAdmin() ? AssetRepair::sum('repair_cost') : 0,
            'averageResolution' => round((clone $base)->whereNotNull('started_at')->whereNotNull('resolved_at')->get()->avg('resolution_minutes') ?? 0),
            'criticalTickets' => $user->isAdmin() ? Ticket::with('user')->where('priority', 'kritis')->whereNotIn('status', ['selesai', 'dibatalkan'])->latest()->limit(5)->get() : collect(),
            'problemAssets' => $user->isAdmin() ? Asset::whereIn('condition', ['perlu_perawatan', 'rusak_ringan', 'rusak_berat'])->limit(6)->get() : collect(),
            'warrantyAssets' => $user->isAdmin() ? Asset::whereBetween('warranty_end_date', [now(), now()->addDays(90)])->orderBy('warranty_end_date')->limit(6)->get() : collect(),
            'recentRepairs' => $user->isAdmin() ? AssetRepair::with(['asset', 'technician'])->latest('repair_date')->limit(5)->get() : collect(),
            'upcomingMaintenance' => in_array($user->role, ['admin', 'technician'], true) ? AssetRepair::with('asset')->whereNotNull('next_maintenance_date')->whereDate('next_maintenance_date', '>=', now())->when($user->isTechnician(), fn ($q) => $q->where('technician_id', $user->id))->orderBy('next_maintenance_date')->limit(5)->get() : collect(),
        ]);
    }

    private function counts(string $model, string $column, array $labels): array
    {
        return ['labels' => collect($labels)->map(fn ($label) => str($label)->replace('_', ' ')->title()), 'values' => collect($labels)->map(fn ($label) => $model::where($column, $label)->count())];
    }
}
