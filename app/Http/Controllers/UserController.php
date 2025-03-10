<?php

namespace App\Http\Controllers;

use App\Domain\CurriculoCases\ListingCurriculoUseCase;
use App\Domain\CurriculoCases\UpdateCurriculoUseCase;
use App\Domain\DTO\UserDTO;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\UpdateCurriculumRequest;
use App\Domain\UseCases\Auth\UpdateCurriculumUseCase;
use App\Http\Requests\Auth\UpdateCurriculoRequest;

class UserController extends Controller
{
    public function __construct()
    {
    }
    // Função para editar o currículo
    public function editCurriculo()
    {
        if (!Auth::check()) {
            redirect()->route('register');
        }
        $user = Auth::user();
        $userDto =  new UserDTO($user, false);
        return Inertia::render('Auth/Register', [
            'user' =>   $userDto,
            'courses' => $userDto->courses,
            'experiences' => $userDto->experiences,
        ]);
    }

    // Função para salvar a edição do currículo
    public function updateCurriculum(UpdateCurriculoRequest $request, UpdateCurriculoUseCase $updateCurriculumUseCase)
    {
        if (!Auth::check()) {
            return redirect()->route('register');
        }

        try {
            $user = $updateCurriculumUseCase->execute( $request->all());
            if ($user instanceof User) {
                return redirect()->route('editar-curriculo')->with('success', 'Currículo atualizado com sucesso');
            }
            throw new \InvalidArgumentException('Erro ao atualizar o currículo');
        } catch (\Throwable $e) {
            return back()->withErrors(['page' => $e->getMessage()]);
        }
    }

    public function showListing(ListingCurriculoUseCase $listingCurriculoUseCase){
        $users = $listingCurriculoUseCase->execute();
        $averageSalary = $listingCurriculoUseCase->averageSalary();
        $totalSalary = $listingCurriculoUseCase->totalSalary();

        return Inertia::render('Curriculos/Show', [
            'users' => $users,
            'averageSalary' => $averageSalary,
            'totalSalary' => $totalSalary
        ]);
    }
}
