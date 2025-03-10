<?php

namespace App\Domain\Services;

use App\Traits\BaseService;
use App\Domain\Repositories\UserCourseRepository;

class UserCourseService
{
    use BaseService;

    public function __construct(
        protected UserCourseRepository $userCourseRepository
    ) {
        $this->setBaseServiceClass($userCourseRepository);
    }
}
