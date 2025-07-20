@php
    use App\Enums\FarmingResourceType;
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <meta
        http-equiv="X-UA-Compatible"
        content="ie=edge"
    >
    <title>{{ $calculation->farmer->name }} | {{ $calculation->cropseason->name }}</title>

    @vite('resources/css/app.css')
    @filamentStyles()

    @filamentScripts()

    <style>
        @font-face {
            font-family: 'Inter';
            src: prince-lookup("Inter"),
                url('https://fonts.gstatic.com/s/inter/v12/UcCO3FwrK3iLTeHuS_fvQtMwCp50KnMw2boKoduKmMEVuLyfAZ9hiA.woff2') format('woff2');
        }

        /* @page {
            margin-right: 2.5em;
            margin-left: 2.5em;
        } */
        body {
            font-family: 'Inter';
        }

        .fi-ta-header {
            padding-top: 0.625rem;
            padding-bottom: 0.625rem;
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .fi-ta-header-toolbar {
            display: none;
        }

        .fi-ta-text-summary> :first-child {
            display: none;
        }

        .fi-ta-header-cell,
        .fi-ta-text {
            padding-top: 0.5rem !important;
            padding-bottom: 0.5rem !important;
        }

        .fi-ta-cell> :first-child {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }

        .fi-ta-col-wrp {
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }

        /* {!! Illuminate\Support\Facades\Vite::content('resources/css/app.css') !!} */
    </style>
</head>

<body class="p-2">
    <div class="mx-auto">
        <!-- Grid -->
        <div class="mb-3 items-center justify-center border-b border-gray-200 pb-2">
            <h2 class="text-center text-lg font-semibold text-gray-700">
                {{ $calculation->farmer->name }} | {{ $calculation->cropseason->name }}
            </h2>
        </div>
        <!-- End Grid -->

        <!-- Grid -->
        <div class="grid grid-cols-2 gap-3">
            <div>
                <x-filament::section :compact="true">
                    <x-slot name="heading">
                        Hisab
                    </x-slot>
                    @livewire(App\Filament\Components\CalculationInfolist::class, [
                        'calculation' => $calculation,
                        'printMode' => true,
                    ])
                </x-filament::section>
            </div>

            <div>
                <div>
                    @livewire(App\Filament\Widgets\LedgersTableWidget::class, [
                        'farmer_id' => $calculation->farmer_id,
                        'crop_season_id' => $calculation->crop_season_id,
                        'farmingResourceTypes' => [FarmingResourceType::Fertilizer, FarmingResourceType::Pesticide],
                        'tableHeading' => 'Dawa & Color',
                        'groupsOnly' => true,
                    ])
                </div>

                <div class="mt-3">
                    @livewire(App\Filament\Widgets\LedgersTableWidget::class, [
                        'farmer_id' => $calculation->farmer_id,
                        'crop_season_id' => $calculation->crop_season_id,
                        'farmingResourceTypes' => [FarmingResourceType::Implement, FarmingResourceType::Seed],
                        'tableHeading' => 'Harr & Bij',
                        'groupsOnly' => true,
                    ])
                </div>

                <div class="mt-3">
                    @livewire(App\Filament\Resources\FarmerResource\Widgets\LoanWidget::class, [
                        'record' => $calculation->farmer,
                    ])
                </div>
            </div>
        </div>

    </div>
</body>

</html>
