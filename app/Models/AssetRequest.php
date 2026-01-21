<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetRequest extends Model
{
    protected $fillable = [
        'requester_id',
        'status',
        'asset_id',
        'approval_id',
        'issued_at',
        'accepted_at',
    ];

    // Create a new asset request
    public static function createRequest($data)
    {
        return self::create($data);
    }

    // Approve a request
    public function approve()
    {
        $this->status = 'approved';
        $this->save();
    }

    // Reject a request
    public function reject()
    {
        $this->status = 'rejected';
        $this->save();
    }

    // Issue an asset
    public function issue()
    {
        $this->status = 'issued';
        $this->issued_at = now();
        $this->save();
    }

    // Accept an issuance
    public function acceptIssuance()
    {
        $this->status = 'accepted';
        $this->accepted_at = now();
        $this->save();
    }

    // Get requests by requester ID
    public static function getByRequester($requesterId)
    {
        return self::where('requester_id', $requesterId)->get();
    }

    // Get requests by status
    public static function getByStatus($status)
    {
        return self::where('status', $status)->get();
    }

    // Additional request management methods can be added below
}
