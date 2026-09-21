<?php

test('guests are redirected to the login page', function () {
    $this->get('/')
        ->assertRedirectToRoute('filament.admin.auth.login');
});

test('the login page returns a successful response', function () {
    $this->get(route('filament.admin.auth.login'))
        ->assertOk();
});
