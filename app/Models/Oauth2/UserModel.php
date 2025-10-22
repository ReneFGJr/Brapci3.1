<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $allowedFields = ['google_id', 'name', 'email', 'picture'];
    protected $returnType = 'array';

    public function firstOrCreate($googleUser)
    {
        $user = $this->where('google_id', $googleUser['sub'])->first();

        if (!$user) {
            $this->insert([
                'google_id' => $googleUser['sub'],
                'name'      => $googleUser['name'],
                'email'     => $googleUser['email'],
                'picture'   => $googleUser['picture']
            ]);
            $user = $this->where('google_id', $googleUser['sub'])->first();
        }

        return $user;
    }
}
