<?php

use App\Models\PotensiLoc;

test('potensi loc model basic instantiation', function () {
    $potensi = new PotensiLoc();
    expect($potensi)->toBeInstanceOf(PotensiLoc::class);
}); 