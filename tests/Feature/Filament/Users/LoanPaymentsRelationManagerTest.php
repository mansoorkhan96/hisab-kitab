<?php

use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\RelationManagers\LoanPaymentsRelationManager;
use App\Models\Loan;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;

use function Pest\Livewire\livewire;

beforeEach(fn () => Filament::setCurrentPanel('admin'));

it('rejects a payment amount greater than the outstanding loan balance', function () {
    $admin = actingAsAdmin();
    $team = $admin->team;
    $farmer = User::factory()->farmer()->create(['team_id' => $team->id]);

    Loan::factory()->create([
        'user_id' => $farmer->id,
        'amount' => 1_000,
    ]);

    expect((float) $farmer->fresh()->outstanding_loan_balance)->toBe(1_000.0);

    livewire(LoanPaymentsRelationManager::class, [
        'ownerRecord' => $farmer,
        'pageClass' => EditUser::class,
    ])
        ->callAction(TestAction::make('create')->table(), data: [
            'amount' => 1_001,
        ])
        ->assertHasFormErrors(['amount' => ['max']]);
});

it('hides create when outstanding loan balance is zero or less', function () {
    $admin = actingAsAdmin();
    $team = $admin->team;
    $farmer = User::factory()->farmer()->create(['team_id' => $team->id]);

    expect((float) $farmer->outstanding_loan_balance)->toBe(0.0);

    livewire(LoanPaymentsRelationManager::class, [
        'ownerRecord' => $farmer,
        'pageClass' => EditUser::class,
    ])
        ->assertSuccessful()
        ->assertActionHidden(TestAction::make('create')->table());
});
