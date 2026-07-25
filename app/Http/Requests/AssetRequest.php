<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return ['asset_category_id' => 'required|exists:asset_categories,id', 'name' => 'required|string|max:255', 'brand' => 'required|string|max:100', 'model' => 'required|string|max:100', 'serial_number' => ['nullable', 'string', 'max:255', Rule::unique('assets')->ignore($this->route('asset'))], 'specifications' => 'nullable|string', 'purchase_date' => 'nullable|date', 'purchase_price' => 'nullable|numeric|min:0', 'warranty_end_date' => 'nullable|date', 'location' => 'required|string|max:255', 'condition' => 'required|in:baik,perlu_perawatan,rusak_ringan,rusak_berat', 'status' => 'required|in:tersedia,digunakan,diperbaiki,rusak,dipinjamkan,tidak_aktif', 'notes' => 'nullable|string'];
    }
}
