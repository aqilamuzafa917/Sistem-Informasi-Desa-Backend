<?php

use App\Models\IndikatorIDM;

test('indikator idm model basic instantiation', function () {
    $indikator = new IndikatorIDM();
    expect($indikator)->toBeInstanceOf(IndikatorIDM::class);
}); 