<?php

namespace App\Domain\DTO;

class UserProfessionalExperienceDTO
{
    public $company;
    public $position;
    public $start_date;
    public $end_date;
    public $is_current;
    public $description;

    public function __construct($experience, $formatData = true)
    {
        $this->company = $experience->company;
        $this->position = $experience->position;
        $this->start_date = $formatData ? $experience->start_date?->format('d/m/Y') : $experience->start_date?->format('Y-m-d');
        $this->end_date = $formatData ? $experience->end_date?->format('d/m/Y') : $experience->end_date?->format('Y-m-d');
        $this->is_current = $experience->is_current;
        $this->description = $experience->description;
    }
}
