<?php

namespace App\Domain\Repositories;

use App\Models\UserProfessionalExperience;
use App\Traits\BaseRepository;

class UserProfessionalExperienceRepository
{
    use BaseRepository;

    public function __construct(private UserProfessionalExperience $userProfessionalExperience)
    {
        $this->setEntityClass($userProfessionalExperience);
    }

}
