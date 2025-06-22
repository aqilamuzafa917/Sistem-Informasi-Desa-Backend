<?php

use App\Models\ProfilDesa;

test('profil desa model basic instantiation', function () {
    $profil = new ProfilDesa();
    expect($profil)->toBeInstanceOf(ProfilDesa::class);
}); 