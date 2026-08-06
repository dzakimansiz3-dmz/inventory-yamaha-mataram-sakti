<?php

namespace App\Services;

use App\Models\Sparepart;

class SparepartService
{
    public function create($data)
    {
        return Sparepart::create($data);
    }

    public function update($id, $data)
    {
        $sparepart = Sparepart::findOrFail($id);
        $sparepart->update($data);

        return $sparepart;
    }
}