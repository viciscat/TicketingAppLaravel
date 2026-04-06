<?php

namespace App\View\Components;

use App\Dashboard\DashboardWidget;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DashboardStatistic extends Component
{
    public DashboardWidget $widgetData;

    /**
     * Create a new component instance.
     */
    public function __construct($widget)
    {
        $this->widgetData = $widget;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.dashboard-statistic');
    }
}
