<?php

namespace App\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AssetController
{
    // Create a new asset
    public function create(Request $request)
    {
        $asset = Asset::create($request->all());
        return Response::json($asset, 201);
    }

    // Read assets with optional filtering
    public function index(Request $request)
    {
        $query = Asset::query();
        if ($request->has('category')) {
            $query->where('category', $request->get('category'));
        }
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }
        $assets = $query->get();
        return Response::json($assets);
    }

    // Show asset details
    public function show($id)
    {
        $asset = Asset::findOrFail($id);
        return Response::json($asset);
    }

    // Update an existing asset
    public function update(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);
        $asset->update($request->all());
        return Response::json($asset);
    }

    // Decommission an asset
    public function decommission($id)
    {
        $asset = Asset::findOrFail($id);
        $asset->status = 'decommissioned';
        $asset->save();
        return Response::json($asset);
    }
}