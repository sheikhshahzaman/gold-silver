<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\JsonResponse;

class PageController extends Controller
{
    /**
     * Published legal/info pages (slug + title) so the app can build a
     * dynamic menu. Content is fetched per-page via show().
     */
    public function index(): JsonResponse
    {
        $pages = Page::published()
            ->orderBy('title')
            ->get(['slug', 'title', 'updated_at'])
            ->map(fn (Page $p) => [
                'slug' => $p->slug,
                'title' => $p->title,
                'updated_at' => $p->updated_at?->toIso8601String(),
            ]);

        return response()->json(['pages' => $pages]);
    }

    /**
     * A single published page. Body is admin-managed HTML (Filament → Pages),
     * so editing it in the admin updates the app automatically.
     */
    public function show(string $slug): JsonResponse
    {
        $page = Page::published()->where('slug', $slug)->first();

        if (!$page) {
            return response()->json(['message' => 'Page not found.'], 404);
        }

        return response()->json([
            'page' => [
                'slug' => $page->slug,
                'title' => $page->title,
                'body' => $page->body,
                'meta_title' => $page->meta_title,
                'meta_description' => $page->meta_description,
                'updated_at' => $page->updated_at?->toIso8601String(),
            ],
        ]);
    }
}
