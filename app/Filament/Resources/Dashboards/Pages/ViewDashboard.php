<?php

namespace App\Filament\Resources\Dashboards\Pages;

use App\Filament\Resources\Dashboards\DashboardResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Gate;

class ViewDashboard extends ViewRecord
{
    protected static string $resource = DashboardResource::class;
    /**
     * Use a custom Blade view for nicer dashboard presentation.
     */
    protected string $view = 'filament.resources.dashboards.view';
    // ocupar toda a largura disponível (não estático, para compatibilidade com Filament BasePage)
    protected Width|string|null $maxContentWidth = Width::Full;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    // Provide view data for the custom blade
    protected function getViewData(): array
    {
        return [
            'dashboard' => $this->getRecord(),
        ];
    }

    // Use the Filament header for title/subtitle
    public function getHeading(): string
    {
        return (string) $this->getRecord()->title;
    }

    public function getSubheading(): ?string
    {
        return $this->getRecord()->description ?? 'Dashboard executivo com métricas de vendas e performance';
    }
}
