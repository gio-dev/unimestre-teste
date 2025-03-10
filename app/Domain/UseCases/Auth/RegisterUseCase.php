<?php
namespace App\Domain\UseCases\Auth;

use App\Domain\Services\UserCourseService;
use App\Domain\Services\UserProfessionalExperienceService;
use App\Domain\Services\UserService;
use Exception;
use Illuminate\Support\Facades\DB;

class RegisterUseCase {

    public function __construct(
        protected UserService $userService,
        protected UserCourseService $userCourseService,
        protected UserProfessionalExperienceService $userProfessionalExperienceService
    ) {}

    /**
     * Summary of execute
     * @param mixed $request
     * @return mixed
     *
     */
    public function execute($request) {
        DB::beginTransaction();
        try {
            // Save user
            $user = $this->userService->storeNew([
                'name' => $request['name'],
                'email' => $request['email'],
                'login' => $request['login'],
                'password' => $request['password'],
                'document' => $request['cpf'],
                'birth' => $request['birth'],
                'gender' => $request['gender'],
                'marital_status' => $request['marital_status'],
                'education_level' => $request['education_level'],
                'salary' => $request['salary'],
            ]);

            // Save courses
            foreach ($request['courses'] as $course) {
                $this->userCourseService->storeNew([
                    'user_id' => $user->id,
                    'name' => $course['name'],
                    'description' => $course['description'],
                    'start_date' => $course['start_date'],
                    'end_date' => $course['end_date'],
                ]);
            }

            // Save experiences
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
