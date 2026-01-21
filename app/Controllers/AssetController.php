<?php

namespace App\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetController
{
    // Display a listing of the assets.
    public function index()
    {
        $assets = Asset::all();
        return response()->json($assets);
    }

    // Show the form for creating a new asset.
    public function create()
    {
        // Return create view (if using a view)
    }

    // Store a newly created asset in storage.
    public function store(Request $request)
    {
        $asset = Asset::create($request->all());
        return response()->json($asset, 201);
    }

    // Display the specified asset.
    public function show($id)
    {
        $asset = Asset::findOrFail($id);
        return response()->json($asset);
    }

    // Show the form for editing the specified asset.
    public function edit($id)
    {
        // Return edit view (if using a view)
    }

    // Update the specified asset in storage.
    public function update(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);
        $asset->update($request->all());
        return response()->json($asset);
    }

    // Remove the specified asset from storage.
    public function destroy($id)
    {
        $asset = Asset::findOrFail($id);
        $asset->delete();
        return response()->json(null, 204);
    }

    // Stock management method.
    public function manageStock($id, Request $request)
    {
        $asset = Asset::findOrFail($id);
        // Assuming there's a 'quantity' field in the request
        $asset->quantity += $request->input('quantity');
        $asset->save();
        return response()->json($asset);
    }
}
