<?php

use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\User;

it('returns loans sum minus payments sum as outstanding loan balance', function () {
    $admin = actingAsAdmin();
    $team = $admin->team;
    $farmer = User::factory()->farmer()->create(['team_id' => $team->id]);

    Loan::factory()->create([
        'user_id' => $farmer->id,
        'amount' => 1_000,
    ]);
    Loan::factory()->create([
        'user_id' => $farmer->id,
        'amount' => 500,
    ]);

    LoanPayment::factory()->create([
        'user_id' => $farmer->id,
        'calculation_id' => null,
        'amount' => 200,
    ]);

    expect((float) $farmer->outstanding_loan_balance)->toBe(1_300.0);
});
