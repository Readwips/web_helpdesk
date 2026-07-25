<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetRepair;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $r, string $type = 'tickets')
    {
        [$headers,$rows] = $this->data($r, $type);
        $lookups = [
            'ticketCategories' => TicketCategory::orderBy('name')->get(),
            'assetCategories' => AssetCategory::orderBy('name')->get(),
            'technicians' => User::where('role', 'technician')->orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
        ];

        return view('reports.index', compact('type', 'headers', 'rows', 'lookups'));
    }

    public function excel(Request $r, string $type)
    {
        [$headers,$rows] = $this->data($r, $type);

        return Excel::download(new ReportExport($rows, $headers), 'laporan-'.$type.'-'.now()->format('Ymd').'.xlsx');
    }

    public function pdf(Request $r, string $type)
    {
        [$headers,$rows] = $this->data($r, $type);

        return Pdf::loadView('reports.pdf', compact('type', 'headers', 'rows'))->setPaper('a4', 'landscape')->download('laporan-'.$type.'-'.now()->format('Ymd').'.pdf');
    }

    private function data(Request $r, string $type): array
    {
        return match ($type) {
            'assets' => $this->assets($r),'repairs' => $this->repairs($r),'technicians' => $this->technicians($r),default => $this->tickets($r)
        };
    }

    private function dates(Builder $q, Request $r, string $column = 'created_at'): Builder
    {
        return $q->when($r->date_from, fn ($x, $v) => $x->whereDate($column, '>=', $v))->when($r->date_to, fn ($x, $v) => $x->whereDate($column, '<=', $v));
    }

    private function tickets(Request $r): array
    {
        $q = $this->dates(Ticket::with(['user.department', 'technician', 'category']), $r)->when($r->status, fn ($x, $v) => $x->where('status', $v))->when($r->priority, fn ($x, $v) => $x->where('priority', $v))->when($r->category, fn ($x, $v) => $x->where('ticket_category_id', $v))->when($r->technician, fn ($x, $v) => $x->where('technician_id', $v))->when($r->department, fn ($x, $v) => $x->whereHas('user', fn ($z) => $z->where('department_id', $v)));
        $rows = $q->latest()->get()->map(fn ($t) => [$t->ticket_number, $t->created_at->format('d-m-Y'), $t->user->name, $t->user->department?->name ?? '-', $t->title, $t->category->name, $t->priority, $t->technician?->name ?? '-', $t->status, $t->resolution_minutes !== null ? $t->resolution_minutes.' menit' : '-'])->all();

        return [['Nomor', 'Tanggal', 'Pelapor', 'Departemen', 'Judul', 'Kategori', 'Prioritas', 'Teknisi', 'Status', 'Durasi'], $rows];
    }

    private function assets(Request $r): array
    {
        $q = Asset::with(['category', 'assignedUser'])->when($r->status, fn ($x, $v) => $x->where('status', $v))->when($r->condition, fn ($x, $v) => $x->where('condition', $v))->when($r->category, fn ($x, $v) => $x->where('asset_category_id', $v));
        $rows = $q->get()->map(fn ($a) => [$a->asset_code, $a->name, $a->category->name, $a->brand, $a->model, $a->serial_number ?? '-', $a->assignedUser?->name ?? '-', $a->location, $a->condition, $a->status, $a->warranty_end_date?->format('d-m-Y') ?? '-'])->all();

        return [['Kode', 'Nama', 'Kategori', 'Merek', 'Model', 'Serial', 'Pengguna', 'Lokasi', 'Kondisi', 'Status', 'Garansi'], $rows];
    }

    private function repairs(Request $r): array
    {
        $q = $this->dates(AssetRepair::with(['asset', 'technician']), $r, 'repair_date')->when($r->technician, fn ($x, $v) => $x->where('technician_id', $v));
        $rows = $q->latest('repair_date')->get()->map(fn ($x) => [$x->repair_date->format('d-m-Y'), $x->asset->asset_code, $x->asset->name, $x->technician->name, $x->diagnosis, $x->repair_action, $x->replaced_components ?? '-', (float) $x->repair_cost, $x->result])->all();

        return [['Tanggal', 'Kode Aset', 'Nama Aset', 'Teknisi', 'Diagnosis', 'Tindakan', 'Komponen', 'Biaya', 'Hasil'], $rows];
    }

    private function technicians(Request $r): array
    {
        $rows = User::where('role', 'technician')->withCount(['assignedTickets as assigned_count', 'assignedTickets as done_count' => fn ($q) => $q->where('status', 'selesai'), 'assignedTickets as active_count' => fn ($q) => $q->whereIn('status', ['ditugaskan', 'diproses', 'menunggu_konfirmasi'])])->get()->map(function ($u) {
            $avg = $u->assignedTickets()->whereNotNull('started_at')->whereNotNull('resolved_at')->get()->avg('resolution_minutes');

            return [$u->name, $u->assigned_count, $u->done_count, $u->active_count, $avg ? round($avg).' menit' : '-'];
        })->all();

        return [['Teknisi', 'Ditugaskan', 'Selesai', 'Aktif', 'Rata-rata Durasi'], $rows];
    }
}
