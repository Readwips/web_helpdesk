<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['ticket_category_id' => 'required|exists:ticket_categories,id', 'asset_id' => 'nullable|exists:assets,id', 'title' => 'required|string|max:255', 'description' => 'required|string|max:5000', 'location' => 'required|string|max:255', 'priority' => 'required|in:rendah,sedang,tinggi,kritis'];
    }
}
