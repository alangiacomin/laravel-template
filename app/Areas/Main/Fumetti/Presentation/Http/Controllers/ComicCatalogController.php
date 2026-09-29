<?php

namespace App\Areas\Main\Fumetti\Presentation\Http\Controllers;

use AlanGiacomin\LaravelCqrs\App\Presentation\Http\Controllers\Controller;
use App\Areas\Admin\Fumetti\Application\Queries\FumettiPageQueries;
use App\Areas\Main\Fumetti\Application\Queries\ComicCatalogQuery;
use App\Areas\Main\Fumetti\Presentation\Http\Requests\ComicCatalogRequest;
use Inertia\Response;
use Inertia\ResponseFactory;

class ComicCatalogController extends Controller
{
    public function __construct(
        private readonly ComicCatalogQuery $catalog,
    ) {}

    public function index(ComicCatalogRequest $request): Response|ResponseFactory
    {
        $filters = $request->validated();
        $data = $this->catalog->execute([
            'q' => $filters['q'] ?? null,
            'testata_id' => isset($filters['testata_id']) ? (int) $filters['testata_id'] : null,
            'serie_id' => isset($filters['serie_id']) ? (int) $filters['serie_id'] : null,
            'year' => isset($filters['year']) ? (int) $filters['year'] : null,
        ]);

        return inertia('App/Comics/Comics', [
            ...$data,
            'filters' => [
                'q' => $filters['q'] ?? '',
                'testata_id' => isset($filters['testata_id']) ? (int) $filters['testata_id'] : null,
                'serie_id' => isset($filters['serie_id']) ? (int) $filters['serie_id'] : null,
                'year' => isset($filters['year']) ? (int) $filters['year'] : null,
            ],
        ]);
    }
}
