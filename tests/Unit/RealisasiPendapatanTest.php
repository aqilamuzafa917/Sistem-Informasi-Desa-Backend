<?php

use App\Models\RealisasiPendapatan;

test('realisasi pendapatan model basic instantiation', function () {
    $pendapatan = new RealisasiPendapatan();
    expect($pendapatan)->toBeInstanceOf(RealisasiPendapatan::class);
}); 