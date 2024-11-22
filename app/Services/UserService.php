<?php
namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function searchByName(string $name)
    {
        // Delegate the search to the repository
        return $this->userRepository->findByName($name);
    }


    public function getUsersByGroupId($groupId)
    {
        return $this->userRepository->getUsersByGroupId($groupId);
    }
}
