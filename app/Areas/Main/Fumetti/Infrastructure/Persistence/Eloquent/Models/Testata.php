<?php

namespace App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models;

use Database\Factories\TestataFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testata extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'testate';

    protected $fillable = [
        'titolo',
    ];

    /**
     * @return HasMany<Albo, $this>
     */
    public function albi(): HasMany
    {
        return $this->hasMany(Albo::class);
    }

    protected static function newFactory(): Factory
    {
        return TestataFactory::new();
    }
}
