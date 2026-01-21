<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;

class User extends Model
{
    protected $fillable = ['name', 'email', 'password', 'role'];

    public static function create(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        return self::query()->create($data);
    }

    public static function findById($id)
    {
        return self::query()->find($id);
    }

    public static function findByEmail($email)
    {
        return self::query()->where('email', $email)->first();
    }

    public static function verifyPassword($user, $password)
    {
        return Hash::check($password, $user->password);
    }

    public static function updateRole($id, $role)
    {
        $user = self::findById($id);
        if ($user) {
            $user->role = $role;
            $user->save();
            return $user;
        }
        return null;
    }
}