<?php

namespace App\Domain\Repositories;

use App\Models\UserCourse;
use App\Traits\BaseRepository;

class UserCourseRepository
{
    use BaseRepository;

    public function __construct(private UserCourse $userCourse)
    {
        $this->setEntityClass($userCourse);
    }

}
