<?php

namespace App\Http\Controllers;

use App\Http\Requests\PackageRequest;
use App\Models\Package;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PackageController extends Controller
{
    /**
     * Display a listing of the packages.
     */
    public function index(): Response
    {
        return Inertia::render('packages/Index', [
            'packages' => Package::query()
                ->with('mode')
                ->withCount('reservations')
                ->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created package.
     */
    public function store(PackageRequest $request): RedirectResponse
    {
        Package::query()->create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Le package a été créé.')]);

        return to_route('packages.index');
    }

    /**
     * Update the given package.
     */
    public function update(PackageRequest $request, Package $package): RedirectResponse
    {
        $package->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Le package a été mis à jour.')]);

        return to_route('packages.index');
    }

    /**
     * Delete the given package.
     */
    public function destroy(Package $package): RedirectResponse
    {
        if ($package->reservations()->exists()) {
            Inertia::flash('toast', [
                'type' => 'error',
                'message' => __('Impossible de supprimer ce package : des réservations y sont associées.'),
            ]);

            return back();
        }

        $package->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Le package a été supprimé.')]);

        return to_route('packages.index');
    }
}
