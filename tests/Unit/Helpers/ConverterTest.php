<?php

use App\Helpers\Converter;

it('formats exact one sack as 1 Bori', function () {
    expect(Converter::kgsToSacksString(100))->toBe('1 Bori');
});

it('formats under one sack as kilograms', function () {
    expect(Converter::kgsToSacksString(50))->toBe('50 KGs');
});

it('formats multiple sacks with remainder', function () {
    expect(Converter::kgsToSacksString(250))->toBe('2 Borion, 50 KGs');
});

it('formats exact multiple sacks with no remainder', function () {
    expect(Converter::kgsToSacksString(200))->toBe('2 Borion');
});

it('formats munns with remainder', function () {
    expect(Converter::kgsToMunnString(null))->toBe('-');
    expect(Converter::kgsToMunnString(39))->toBe('39');
    expect(Converter::kgsToMunnString(40))->toBe('1-0');
    expect(Converter::kgsToMunnString(85))->toBe('2-5');
});
