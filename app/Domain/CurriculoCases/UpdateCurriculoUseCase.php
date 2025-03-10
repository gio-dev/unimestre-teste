<?php
namespace App\Domain\CurriculoCases;

use App\Domain\Services\UserCourseService;
use App\Domain\Services\UserProfessionalExperienceService;
use App\Domain\Services\UserService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateCurriculoUseCase {

    public function __construct(
        protected UserService $userService,
        protected UserCourseService $userCourseService,
        protected UserProfessionalExperienceService $userProfessionalExperienceService
    ) {}

    public function execute($request) {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            // Update user
            $userData = [
                'name' => $request['name'],
                'email' => $request['email'],
                'login' => $request['login'],
                'document' => $request['cpf'],
                'birth' => $request['birth'],
                'gender' => $request['gender'],
                'marital_status' => $request['marital_status'],
                'education_level' => $request['education_level'],
                'salary' => $request['salary'],
            ];

            if (!empty($request['password'])) {
                $userData['password'] = $request['password'];
            }

            $user = $this->userService->changeById($user->id, $userData);
            
            // Update courses
            $this->userCourseService->setIdentify('user_id')->deleteAllByIdentify($user->id);
            foreach ($request['courses'] as $course) {
                $this->userCourseService->storeNew([
                    'user_id' => $user->id,
                    'name' => $course['name'],
                    'description' => $course['description'],
                    'start_date' => $course['start_date'],
                    'end_date' => $course['end_date'],
                ]);
            }

            // Update experiences
            $this->userProfessionalExperienceService->setIdentify('user_id')->deleteAllByIdentify($user->id);
            foreach ($request['experiences'] as $experience) {
                $this->userProfessionalExperienceService->storeNew([
                    'user_id' => $user->id,
                    'company' => $experience['company'],
                    'position' => $experience['position'],
                    'start_date' => $experience['start_date'],
                    'end_date' => $experience['end_date'],
                    'is_current' => $experience['is_current'],
                    'description' => $experience['description'],
                ]);
            }

            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            // Handle error (e.g., log it, rethrow it, etc.)
            throw $e;
        }
    }
}
