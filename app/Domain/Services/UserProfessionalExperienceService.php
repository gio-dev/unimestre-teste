<?php

namespace App\Domain\Services;

use App\Traits\BaseService;
use App\Domain\Repositories\UserProfessionalExperienceRepository;

class UserProfessionalExperienceService
{
    use BaseService;

    public function __construct(
        protected UserProfessionalExperienceRepository $userProfessionalExperienceRepository
    ) {
        $this->setBaseServiceClass($userProfessionalExperienceRepository);
    }
}
