<?php

namespace App\Policies;

use App\Models\KnowledgeArticle;
use App\Models\User;

class KnowledgeArticlePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, KnowledgeArticle $article): bool
    {
        return $article->status === 'published' || $user->isAdmin() || ($user->isTechnician() && $article->author_id === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isTechnician();
    }

    public function update(User $user, KnowledgeArticle $article): bool
    {
        return $user->isAdmin() || ($user->isTechnician() && $article->author_id === $user->id);
    }

    public function delete(User $user, KnowledgeArticle $article): bool
    {
        return $user->isAdmin();
    }
}
