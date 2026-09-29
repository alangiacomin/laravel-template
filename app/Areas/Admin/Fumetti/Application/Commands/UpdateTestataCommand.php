<?php

namespace App\Areas\Admin\Fumetti\Application\Commands;

use AlanGiacomin\LaravelCqrs\App\Application\Commands\Command;
use App\Areas\Admin\Fumetti\Application\Data\TestataInputData;
use App\Areas\Main\Fumetti\Infrastructure\Persistence\Eloquent\Models\Testata;

class UpdateTestataCommand extends Command
{
    public function __construct(
        public int $id,
        public TestataInputData $data,
    ) {}

    public function handle(): void
    {
        Testata::query()->findOrFail($this->id)->update($this->data->toArray());
    }
}
