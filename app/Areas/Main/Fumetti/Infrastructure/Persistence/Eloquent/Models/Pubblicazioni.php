<?php

namespace App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pubblicazioni extends Model
{
    use SoftDeletes;

    protected $table = 'pubblicazioni';

    protected $fillable = [
        'albo_id',
        'serie_id',
        'numero',
        'numero_gruppo',
        'data_pubblicazione',
    ];

    /**
     * @return BelongsTo<Albo, $this>
     */
    public function albo(): BelongsTo
    {
        return $this->belongsTo(Albo::class);
    }

    /**
     * @return BelongsTo<Serie, $this>
     */
    public function serie(): BelongsTo
    {
        return $this->belongsTo(Serie::class);
    }

    protected function casts(): array
    {
        return [
            'numero' => 'integer',
            'numero_gruppo' => 'integer',
            'data_pubblicazione' => 'date',
        ];
    }
}
