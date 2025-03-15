<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\Post;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class Stats extends BaseWidget
{

    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        return [];
    }
}
