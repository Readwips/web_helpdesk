<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KnowledgeArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['knowledge_category_id' => 'required|exists:knowledge_categories,id', 'title' => 'required|string|max:255', 'summary' => 'required|string|max:2000', 'cause' => 'required|string|max:5000', 'solution_steps' => 'required|string|max:10000', 'additional_notes' => 'nullable|string|max:5000', 'status' => 'required|in:draft,published,archived'];
    }
}
