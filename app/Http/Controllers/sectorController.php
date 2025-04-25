<?php

namespace App\Http\Controllers;

use App\Models\Sector;
use Illuminate\Http\Request;

class sectorController extends Controller
{
    public function getSectors()
    {
        $sectors = Sector::all();
        return response()->json([
            'sectors' => $sectors
        ], 200);
    }
    public function createSector(Request $request)
    {
        $sector = Sector::create($request->all());
        return response()->json([
            'sector' => $sector
        ], 201);
    }
    public function updateSector(Request $request, $id)
    {
        $sector = Sector::find($id);
        $sector->update($request->all());
        return response()->json([
            'sector' => $sector
        ], 200);
    }
    public function deleteSector($id)
    {
        $sector = Sector::find($id);
        $sector->delete();
        return response()->json([
            'message' => 'Sector deleted successfully'
        ], 200);
    }   
}
