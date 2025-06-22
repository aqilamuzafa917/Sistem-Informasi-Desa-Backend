<?php

use App\Models\Pengaduan;

test('pengaduan model basic instantiation', function () {
    $pengaduan = new Pengaduan();
    expect($pengaduan)->toBeInstanceOf(Pengaduan::class);
}); 