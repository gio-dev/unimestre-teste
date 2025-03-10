<?php
namespace App\Domain\CurriculoCases;

use App\Domain\Services\UserService;
use App\Domain\DTO\UserDTO;
use Illuminate\Pagination\LengthAwarePaginator;

class ListingCurriculoUseCase {

    public function __construct(
        protected UserService $userService
    ) {}

    public function execute(): LengthAwarePaginator {
        $users = $this->userService->setLoad(['courses', 'experiences'])->paginateDefault();
        $userDTOs = $users->getCollection()->map(function($user) {
            return new UserDTO($user);
        });

        return new LengthAwarePaginator(
            $userDTOs,
            $users->total(),
            $users->perPage(),
            $users->currentPage(),
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    public function averageSalary(): float {
        return $this->userService->averageSalary();
    }

    public function totalSalary(): float {
        return $this->userService->totalSalary();
    }
}
