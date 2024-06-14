<?php

namespace App\Services;

use App\Exceptions\ValidationException;
use App\Interfaces\UserRepositoryInterface;
use Carbon\Carbon;

class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ){}

    public function create(array $data)
    {
        $data['password'] = bcrypt($data['password']);
        $customer = $this->userRepository->createUser($data);
        return $customer;
    }


    public function get($userId)
    {
        return $this->userRepository->getUserById($userId);
    }

    public function login($email, $password)
    {

        $token = auth()->guard('api')->attempt(['email' => $email, 'password' => $password]);
        if (!$token)
            throw new ValidationException(__('Wrong credentials'));
        return $token;
    }

    public function updatePassword($userId, $oldPassword, $newPassword)
    {
        $this->userRepository->updatePassword($userId, $oldPassword, $newPassword);
    }

    public function getProfile($userId){
        return $this->userRepository->getUserById($userId);
    }

}
