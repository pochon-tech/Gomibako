<?php
namespace App\Repositories;

use App\Entities;
use App\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    /**
     * @param array $request_params
     * @return Entities\User
     * @throws \Exception
     */
    public function new(array $request_params): Entities\User
    {

        return (new UserFactory)->make(
            $request_params['name'],
            $request_params['email'],
            $request_params['password']
        );
    }

    /**
     * @return Collection
     */
    public function list(): Collection
    {
        // Eloquentモデル
        return User::all();
    }

    public function store(Entities\User $user): bool
    {
        return (new User([
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword()
        ]))->save();
    }
}