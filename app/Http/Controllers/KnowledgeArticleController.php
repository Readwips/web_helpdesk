<?php

namespace App\Http\Controllers;

use App\Http\Requests\KnowledgeArticleRequest;
use App\Models\KnowledgeArticle;
use App\Models\KnowledgeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KnowledgeArticleController extends Controller
{
    public function index(Request $r)
    {
        $this->authorize('viewAny', KnowledgeArticle::class);
        $q = KnowledgeArticle::with(['category', 'author'])->when($r->user()->role === 'user', fn ($x) => $x->where('status', 'published'))->when($r->user()->isTechnician(), fn ($x) => $x->where(fn ($a) => $a->where('status', 'published')->orWhere('author_id', $r->user()->id)))->when($r->q, fn ($x, $s) => $x->where(fn ($a) => $a->where('title', 'like', "%$s%")->orWhere('summary', 'like', "%$s%")))->when($r->category, fn ($x, $v) => $x->where('knowledge_category_id', $v));

        return view('knowledge.index', ['articles' => $q->orderByDesc('published_at')->paginate(12)->withQueryString(), 'categories' => KnowledgeCategory::all(), 'popular' => KnowledgeArticle::where('status', 'published')->orderByDesc('view_count')->limit(5)->get()]);
    }

    public function create()
    {
        $this->authorize('create', KnowledgeArticle::class);

        return view('knowledge.form', ['article' => new KnowledgeArticle, 'categories' => KnowledgeCategory::all()]);
    }

    public function store(KnowledgeArticleRequest $r)
    {
        $this->authorize('create', KnowledgeArticle::class);
        $data = $r->validated();
        if ($r->user()->isTechnician() && $data['status'] === 'archived') {
            $data['status'] = 'draft';
        }$article = KnowledgeArticle::create($data + ['author_id' => $r->user()->id, 'slug' => $this->slug($data['title']), 'published_at' => $data['status'] === 'published' ? now() : null]);

        return to_route('knowledge.show', $article)->with('success', 'Artikel disimpan.');
    }

    public function show(KnowledgeArticle $article)
    {
        $this->authorize('view', $article);
        if ($article->status === 'published') {
            $article->increment('view_count');
        }$article->load(['category', 'author']);
        $related = KnowledgeArticle::where('status', 'published')->where('knowledge_category_id', $article->knowledge_category_id)->whereKeyNot($article->id)->limit(4)->get();

        return view('knowledge.show', compact('article', 'related'));
    }

    public function edit(KnowledgeArticle $article)
    {
        $this->authorize('update', $article);

        return view('knowledge.form', ['article' => $article, 'categories' => KnowledgeCategory::all()]);
    }

    public function update(KnowledgeArticleRequest $r, KnowledgeArticle $article)
    {
        $this->authorize('update', $article);
        $data = $r->validated();
        if ($r->user()->isTechnician() && $data['status'] === 'archived') {
            $data['status'] = 'draft';
        }$data['slug'] = $this->slug($data['title'], $article->id);
        $data['published_at'] = $data['status'] === 'published' ? ($article->published_at ?? now()) : null;
        $article->update($data);

        return to_route('knowledge.show', $article)->with('success', 'Artikel diperbarui.');
    }

    public function destroy(KnowledgeArticle $article)
    {
        $this->authorize('delete', $article);
        $article->delete();

        return to_route('knowledge.index')->with('success', 'Artikel dihapus.');
    }

    private function slug(string $title, ?int $ignore = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $n = 2;
        while (KnowledgeArticle::withTrashed()->where('slug', $slug)->when($ignore, fn ($q) => $q->whereKeyNot($ignore))->exists()) {
            $slug = $base.'-'.$n++;
        }

return $slug;
    }
}
