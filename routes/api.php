<?php

use Abdukhaligov\LaravelPageable\Pageable;
use App\Http\Resources\GameResource;
use App\Http\Resources\PaginationCollection;
use App\Models\Device;
use App\Models\Game;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.server')->get('/', function () {
    return Auth::guard('server')->user();
});

Route::get('/instance-info', function () {
    return response()->json([
        'instance_id' => gethostname(),
    ]);
});
