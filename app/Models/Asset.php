<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'asset_code',
        'name',
        'category',
        'serial_number',
        'location',
        'cost',
        'stock_quantities',
    ];

    public static function createAsset(array $data)
    {
        return self::create($data);
    }

    public static function readAsset($id)
    {
        return self::find($id);
    }

    public static function updateAsset($id, array $data)
    {
        $asset = self::find($id);
        if ($asset) {
            $asset->update($data);
            return $asset;
        }
        return null;
    }

    public static function deleteAsset($id)
    {
        return self::destroy($id);
    }

    public function track() {
        // Implementation for asset tracking
    }

    public function manageStock() {
        // Implementation for stock control
    }

    public function manageStatus() {
        // Implementation for status management
    }
}
