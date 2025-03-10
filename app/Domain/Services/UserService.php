<?php

namespace App\Domain\Services;

use App\Domain\Repositories\UserRepository;
use App\Traits\BaseService;

class UserService
{
    use BaseService;

    public function __construct(
        protected UserRepository $userRepository
    ) {
        $this->setBaseServiceClass($userRepository);
    }

    public function averageSalary(): float
    {
        return $this->userRepository->averageSalary();
    }

    public function totalSalary(): float
    {
        return $this->userRepository->totalSalary();
    }

}
