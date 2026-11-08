<?php

namespace App\Models;

use Database\Factories\ReservationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string|null $reference
 * @property string $nom
 * @property string $business
 * @property int $mode_id
 * @property int $package_id
 * @property Carbon $creneau
 * @property bool $is_confirm
 * @property bool $is_cancelled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Mode $mode
 * @property Package $package
 */
#[Fillable(['nom', 'business', 'mode_id', 'package_id', 'creneau', 'is_confirm', 'is_cancelled'])]
class Reservation extends Model
{
    /** @use HasFactory<ReservationFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'creneau' => 'datetime',
            'is_confirm' => 'boolean',
            'is_cancelled' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (Reservation $reservation): void {
            if (blank($reservation->reference)) {
                $reservation->forceFill([
                    'reference' => self::buildReference($reservation->id),
                ])->saveQuietly();
            }
        });
    }

    /**
     * Build the reference from the current year and the reservation id.
     */
    public static function buildReference(int $id): string
    {
        return sprintf('OCD-%d-%06d', now()->year, $id);
    }

    /**
     * Get the mode of the reservation.
     *
     * @return BelongsTo<Mode, $this>
     */
    public function mode(): BelongsTo
    {
        return $this->belongsTo(Mode::class);
    }

    /**
     * Get the package of the reservation.
     *
     * @return BelongsTo<Package, $this>
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
