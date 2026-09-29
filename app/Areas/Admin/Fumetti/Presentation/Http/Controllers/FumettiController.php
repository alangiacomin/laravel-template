<?php

namespace App\Areas\Admin\Fumetti\Presentation\Http\Controllers;

use AlanGiacomin\LaravelCqrs\App\Presentation\Http\Controllers\Controller;
use App\Areas\Admin\Fumetti\Application\Commands\CreateAlboCommand;
use App\Areas\Admin\Fumetti\Application\Commands\CreateSerieCommand;
use App\Areas\Admin\Fumetti\Application\Commands\CreateTestataCommand;
use App\Areas\Admin\Fumetti\Application\Commands\DeleteAlboCommand;
use App\Areas\Admin\Fumetti\Application\Commands\DeleteSerieCommand;
use App\Areas\Admin\Fumetti\Application\Commands\DeleteTestataCommand;
use App\Areas\Admin\Fumetti\Application\Commands\UpdateAlboCommand;
use App\Areas\Admin\Fumetti\Application\Commands\UpdateSerieCommand;
use App\Areas\Admin\Fumetti\Application\Commands\UpdateTestataCommand;
use App\Areas\Admin\Fumetti\Application\Queries\FumettiPageQueries;
use App\Areas\Admin\Fumetti\Presentation\Http\Requests\AlbiFilterRequest;
use App\Areas\Admin\Fumetti\Presentation\Http\Requests\AlboRequest;
use App\Areas\Admin\Fumetti\Presentation\Http\Requests\SerieRequest;
use App\Areas\Admin\Fumetti\Presentation\Http\Requests\TestataRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Inertia\Response;
use Inertia\ResponseFactory;

#[Middleware('auth')]
#[Middleware('not_banned')]
class FumettiController extends Controller
{
    public function __construct(
        private readonly FumettiPageQueries $queries,
    ) {}

    public function testate(): Response|ResponseFactory
    {
        return inertia('Admin/Fumetti/Testate', [
            'testate' => $this->queries->testate(),
        ]);
    }

    public function testata(int $id): Response|ResponseFactory
    {
        return inertia('Admin/Fumetti/Testata', [
            'testata' => $this->queries->testata($id),
        ]);
    }

    public function creaTestata(TestataRequest $request): RedirectResponse
    {
        dispatch_sync(new CreateTestataCommand($request->payloadData()));

        return back()->with('success', __('admin.comic_created'));
    }

    public function aggiornaTestata(int $id, TestataRequest $request): RedirectResponse
    {
        dispatch_sync(new UpdateTestataCommand($id, $request->payloadData()));

        return back()->with('success', __('admin.comic_updated'));
    }

    public function eliminaTestata(int $id): RedirectResponse
    {
        dispatch_sync(new DeleteTestataCommand($id));

        return to_route(app()->getLocale().'.admin.testate')->with('success', __('admin.comic_deleted'));
    }

    public function serie(): Response|ResponseFactory
    {
        return inertia('Admin/Fumetti/Serie', [
            'serie' => $this->queries->serie(),
            'testate' => $this->queries->testateOptions(),
        ]);
    }

    public function serieDettaglio(int $id): Response|ResponseFactory
    {
        return inertia('Admin/Fumetti/SerieDettaglio', [
            'serie' => $this->queries->serieDettaglio($id),
            'testate' => $this->queries->testateOptions(),
        ]);
    }

    public function creaSerie(SerieRequest $request): RedirectResponse
    {
        dispatch_sync(new CreateSerieCommand($request->payloadData()));

        return back()->with('success', __('admin.comic_created'));
    }

    public function aggiornaSerie(int $id, SerieRequest $request): RedirectResponse
    {
        dispatch_sync(new UpdateSerieCommand($id, $request->payloadData()));

        return back()->with('success', __('admin.comic_updated'));
    }

    public function eliminaSerie(int $id): RedirectResponse
    {
        dispatch_sync(new DeleteSerieCommand($id));

        return to_route(app()->getLocale().'.admin.serie')->with('success', __('admin.comic_deleted'));
    }

    public function albi(AlbiFilterRequest $request): Response|ResponseFactory
    {
        $filters = $request->validated();
        $testataId = isset($filters['testata_id']) ? (int) $filters['testata_id'] : null;
        $serieId = $testataId !== null && isset($filters['serie_id'])
            ? (int) $filters['serie_id']
            : null;

        return inertia('Admin/Fumetti/Albi', [
            'albi' => $this->queries->albi($testataId, $serieId),
            'serie' => $this->queries->serieOptions($testataId),
            'testate' => $this->queries->testateOptions(),
            'filters' => [
                'testata_id' => $testataId,
                'serie_id' => $serieId,
            ],
        ]);
    }

    public function albo(int $id): Response|ResponseFactory
    {
        return inertia('Admin/Fumetti/Albo', [
            'albo' => $this->queries->albo($id),
            'serie' => $this->queries->serieOptions(),
        ]);
    }

    public function creaAlbo(AlboRequest $request): RedirectResponse
    {
        dispatch_sync(new CreateAlboCommand($request->payloadData()));

        return back()->with('success', __('admin.comic_created'));
    }

    public function aggiornaAlbo(int $id, AlboRequest $request): RedirectResponse
    {
        dispatch_sync(new UpdateAlboCommand($id, $request->payloadData()));

        return back()->with('success', __('admin.comic_updated'));
    }

    public function eliminaAlbo(int $id): RedirectResponse
    {
        dispatch_sync(new DeleteAlboCommand($id));

        return to_route(app()->getLocale().'.admin.albi')->with('success', __('admin.comic_deleted'));
    }
}
