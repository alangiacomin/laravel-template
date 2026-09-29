<?php

namespace App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models;

use Database\Factories\SerieFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Serie extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'serie';

    protected $fillable = [
        'titolo',
    ];

    /**
     * @return HasMany<Pubblicazioni, $this>
     */
    public function pubblicazioni(): HasMany
    {
        return $this->hasMany(Pubblicazioni::class);
    }

    protected static function newFactory(): Factory
    {
        return SerieFactory::new();
    }
}
