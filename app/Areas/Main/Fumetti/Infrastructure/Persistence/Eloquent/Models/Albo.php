<?php

namespace App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models;

use Database\Factories\AlboFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Albo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'albi';

    protected $fillable = [
        'titolo',
        'testata_id',
    ];

    /**
     * @return BelongsTo<Testata, $this>
     */
    public function testata(): BelongsTo
    {
        return $this->belongsTo(Testata::class);
    }

    /**
     * @return HasMany<Pubblicazioni, $this>
     */
    public function pubblicazioni(): HasMany
    {
        return $this->hasMany(Pubblicazioni::class);
    }

    protected static function newFactory(): Factory
    {
        return AlboFactory::new();
    }
}
