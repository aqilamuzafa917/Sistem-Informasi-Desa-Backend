<?php

use App\Models\TotalApbDesa;

test('total apb desa model basic instantiation', function () {
    $total = new TotalApbDesa();
    expect($total)->toBeInstanceOf(TotalApbDesa::class);
}); 