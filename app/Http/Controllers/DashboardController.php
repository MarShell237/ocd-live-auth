<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function __invoke(): Response
    {
        $now = now();

        return Inertia::render('Dashboard', [
            'stats' => [
                'toValidate' => Reservation::query()
                    ->where('is_confirm', false)
                    ->where('is_cancelled', false)
                    ->count(),
                'confirmed' => Reservation::query()
                    ->where('is_confirm', true)
                    ->where('is_cancelled', false)
                    ->where('creneau', '>=', $now)
                    ->count(),
                'today' => Reservation::query()
                    ->where('is_cancelled', false)
                    ->whereDate('creneau', $now->toDateString())
                    ->count(),
                'freeSlots' => Reservation::query()
                    ->where('is_confirm', false)
                    ->where('is_cancelled', false)
                    ->where('creneau', '>=', $now)
                    ->count(),
            ],
            'reservations' => Reservation::query()
                ->with('package', 'mode')
                ->take(30)
                ->get()
                ->map(fn (Reservation $reservation): array => [
                    'id' => $reservation->id,
                    'reference' => $reservation->reference ?? '',
                    'nom' => $reservation->nom,
                    'business' => $reservation->business,
                    'mode' => $reservation->mode->name,
                    'package' => $reservation->package->name,
                    'creneau' => $reservation->creneau->format('Y-m-d\TH:i:s'),
                    'status' => $reservation->is_cancelled
                        ? 'cancelled'
                        : ($reservation->is_confirm ? 'confirmed' : 'pending'),
                ]),
        ]);
    }
}
