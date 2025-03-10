<?php

namespace App\Domain\DTO;

class UserCourseDTO
{
    public $name;
    public $description;
    public $start_date;
    public $end_date;

    public function __construct($course, $formatData = true)
    {
        $this->name = $course->name;
        $this->description = $course->description;
        $this->start_date = $formatData ? $course->start_date?->format('d/m/Y') : $course->start_date?->format('Y-m-d');
        $this->end_date = $formatData ? $course->end_date?->format('d/m/Y') : $course->end_date?->format('Y-m-d');
    }
}
