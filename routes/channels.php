<?php

use App\Areas\Main\Auth\Presentation\Http\Controllers\BroadcastController;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', BroadcastController::class);
