<?php

namespace App\Dashboard;

interface DashboardWidget
{
    public function iconPath() : string;
    public function title() : string;
    public function value() : string;
    public function clickRoute() : string|null;
}
