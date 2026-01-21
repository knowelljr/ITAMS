<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class User extends Model
{
    protected $fillable = ['name', 'email', 'password'];

    public static function createUser($name, $email, $password)
    {
        return self::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);
    }

    public static function findByEmail($email)
    {
        return self::where('email', $email)->first();
    }

    public function verifyPassword($password)
    {
        return Hash::check($password, $this->password);
    }

    public static function updateUser($id, $data)
    {
        $user = self::find($id);
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        return $user->update($data);
    }

    public static function deleteUser($id)
    {
        return self::destroy($id);
    }
}