<?php

namespace App\Interfaces;

interface UserRepositoryInterface
{
    public function createUser(array $data);
    public function updateUser($userId, array $data);
    public function getUserById($userId);
    public function updatePassword($userId, $oldPassword, $newPassword);
    public function resetPassword($userId, $newPassword);
}
