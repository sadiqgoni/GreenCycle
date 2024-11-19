<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Session\Session;

class Greencyclewidgets extends Widget
{
    protected static ?int $sort = -2;
    protected static string $view = 'filament.widgets.greencycle-widget';
}