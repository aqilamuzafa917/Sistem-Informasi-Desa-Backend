<?php

use App\Models\VariabelIDM;

test('variabel idm model basic instantiation', function () {
    $variabel = new VariabelIDM();
    expect($variabel)->toBeInstanceOf(VariabelIDM::class);
}); 