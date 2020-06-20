<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    /** Repository */
    private $user_repos;

    public function __construct()
    {
        $this->user_repos = new UserRepository;
    }
    
    public function list(): Collection
    {
        $this->user_repos->list();
    }

    public function store(array $request_params): void
    {
        try {
            $user = $this->user_repos->new($request_params);
            return $this->user_repos->store($user);
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }
    
}