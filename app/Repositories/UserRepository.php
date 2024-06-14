<?php

namespace App\Repositories;

use App\Exceptions\InvalidEntityException;
use App\Exceptions\NotFoundException;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{

    /**
     * create new customer 
     * @param array 
     * @return Modules\Customer\Models\Customer
     */
    public function createUser(array $data)
    {
        $user = User::create($data);
        $user->wallet()->create(['amount'=>0]);
        return $user;
    }

    public function getUserById($userId)
    {
        $user = User::find($userId);
        if (!$user)
            throw new NotFoundException("User Not Found");
        return $user;
    }
    
    public function updateUser($userId, array $data){
        $this->getUserById($userId)->update($data);
    }
    
    public function delete($userId)
    {
        return $this->getUserById($userId)->delete();
    }

    
    public function updatePassword($userId, $oldPassword, $newPassword)
    {
        $customer = $this->getUserById($userId);
        if (Hash::check($oldPassword, $customer->password))
            return $this->resetPassword($userId, $newPassword);
        else
            throw new InvalidEntityException('Old password is incorrect');
    }

    public function resetPassword($userId, $newPassword)
    {
        $customer = $this->getUserById($userId);
        return $customer->update(['password' => bcrypt($newPassword)]);
    }

}
