<?php

namespace App\Areas\Admin\Users\Application\Jobs;

use App\Models\User;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateUserJob
{
    use Dispatchable;

    public function __construct(
        public int $id,
        public string $name,
    ) {}

    public function handle(): void
    {
        User::findOrFail($this->id)->update(['name' => $this->name]);
    }
}
