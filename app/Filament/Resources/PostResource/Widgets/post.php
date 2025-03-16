<?php

namespace App\Filament\Resources\PostResource\Widgets;

use App\Models\Post;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PostStat extends BaseWidget
{
    protected function getStats(): array
    {
        $time = Carbon::today();
        return [
            Stat::make('Total Posts', Post::all()->count()),
            Stat::make('Approved Posts', Post::where('approved', true)->count()),
            Stat::make('Non Approved Posts', Post::where('approved', false)->count()),
            Stat::make('Posts today', Post::whereDate('created_at', $time)->count()),
        ];
    }
}
