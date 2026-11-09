<?php

namespace App\Http\Controllers;

use App\Http\Requests\ModeRequest;
use App\Models\Mode;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ModeController extends Controller
{
    /**
     * Display a listing of the modes.
     */
    public function index(): Response
    {
        return Inertia::render('modes/Index', [
            'modes' => Mode::query()
                ->withCount('reservations')
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created mode.
     */
    public function store(ModeRequest $request): RedirectResponse
    {
        Mode::query()->create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Le mode a été créé.')]);

        return to_route('modes.index');
    }

    /**
     * Update the given mode.
     */
    public function update(ModeRequest $request, Mode $mode): RedirectResponse
    {
        $mode->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Le mode a été mis à jour.')]);

        return to_route('modes.index');
    }

    /**
     * Delete the given mode.
     */
    public function destroy(Mode $mode): RedirectResponse
    {
        if ($mode->reservations()->exists()) {
            Inertia::flash('toast', [
                'type' => 'error',
                'message' => __('Impossible de supprimer ce mode : des réservations y sont associées.'),
            ]);

            return back();
        }

        $mode->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Le mode a été supprimé.')]);

        return to_route('modes.index');
    }
}
