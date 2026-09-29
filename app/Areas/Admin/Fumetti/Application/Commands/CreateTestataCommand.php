<?php

namespace App\Areas\Admin\Fumetti\Application\Commands;

use AlanGiacomin\LaravelCqrs\App\Application\Commands\Command;
use App\Areas\Admin\Fumetti\Application\Data\TestataInputData;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Testata;

class CreateTestataCommand extends Command
{
    public function __construct(
        public TestataInputData $data,
    ) {}

    public function handle(): void
    {
        Testata::query()->create($this->data->toArray());
    }
}
