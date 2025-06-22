<?php

use App\Models\Artikel;

test('artikel model basic instantiation', function () {
    $artikel = new Artikel();
    expect($artikel)->toBeInstanceOf(Artikel::class);
}); 