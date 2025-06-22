<?php

use App\Models\RealisasiBelanja;

test('realisasi belanja model basic instantiation', function () {
    $belanja = new RealisasiBelanja();
    expect($belanja)->toBeInstanceOf(RealisasiBelanja::class);
}); 