<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;

class ReservationController extends Controller
{
public function confirm(Reservation $reservation)
{
    // Mise à jour du booléen
    $t = $reservation->update([
        'is_confirm' => true
    ]);
// dump("i am in the reservation controller");
// dump($reservation);
    // Redirection ou retour en arrière avec un message flash
    return back()->with('success', 'Réservation confirmée avec succès.');
}    
public function cancel(Reservation $reservation)
{
    // Mise à jour du booléen
    $t = $reservation->update([
        'is_cancelled' => true
    ]);
// dump("i am in the reservation controller");
// dump($reservation);
    // Redirection ou retour en arrière avec un message flash
    return back()->with('success', 'Réservation confirmée avec succès.');
}    
}
