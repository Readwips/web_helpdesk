<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssetRepairRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['admin', 'technician'], true);
    }

    public function rules(): array
    {
        return ['ticket_id' => 'nullable|exists:tickets,id', 'repair_date' => 'required|date', 'complaint' => 'required|string', 'diagnosis' => 'required|string', 'repair_action' => 'required|string', 'replaced_components' => 'nullable|string', 'repair_cost' => 'required|numeric|min:0', 'result' => 'required|in:berhasil,berhasil_sebagian,tidak_dapat_diperbaiki,perlu_vendor,perlu_penggantian', 'notes' => 'nullable|string', 'next_maintenance_date' => 'nullable|date|after_or_equal:repair_date', 'asset_condition' => 'required|in:baik,perlu_perawatan,rusak_ringan,rusak_berat', 'asset_status' => 'required|in:tersedia,digunakan,diperbaiki,rusak,tidak_aktif'];
    }
}
