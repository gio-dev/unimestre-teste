<?php

namespace App\Domain\Repositories;

use App\Models\User;
use App\Traits\BaseRepository;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    use BaseRepository;

    public function __construct(private User $user)
    {
        $this->setEntityClass($user);
    }


    public function averageSalary(): float
    {
        return $this->entity->select(DB::raw('AVG(COALESCE(salary, 0)) as average_salary'))->value('average_salary');
    }

    public function totalSalary(): float
    {
        return $this->user->sum('salary');
    }
}
