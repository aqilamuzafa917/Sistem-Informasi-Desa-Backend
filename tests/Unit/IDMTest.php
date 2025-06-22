<?php

use App\Models\IDM;

test('idm model basic instantiation', function () {
    $idm = new IDM();
    expect($idm)->toBeInstanceOf(IDM::class);
}); 