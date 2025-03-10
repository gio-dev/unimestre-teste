<?php

namespace App\Domain\DTO;

class UserDTO
{
    public $name;
    public $email;
    public $login;
    public $document;
    public $birth;
    public $gender;
    public $marital_status;
    public $education_level;
    public $salary;
    public $courses;
    public $experiences;

    public function __construct($user, $formatData = true)
    {
        $this->name = $user->name;
        $this->email = $user->email;
        $this->login = $user->login;
        $this->document = $user->document;
        $this->birth = $formatData ? $user->birth?->format('d/m/Y') : $user->birth->format('Y-m-d');
        $this->gender = $user->gender;
        $this->marital_status = $user->marital_status;
        $this->education_level = $user->education_level;
        $this->salary = $user->salary ?? 0;
        $this->courses = $user->courses->map(function($course) use ($formatData) {
            return new UserCourseDTO($course, $formatData);
        });
        $this->experiences = $user->experiences->map(function($experience) use ($formatData) {
            return new UserProfessionalExperienceDTO($experience, $formatData);
        });
    }
}
