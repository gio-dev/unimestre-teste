<?php

namespace App\Http\Controllers\Auth;

use App\Domain\UseCases\Auth\RegisterUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterUserRequest $request, RegisterUseCase $registerUseCase): RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('editar-curriculo');
        }

        try {
            $user = $registerUseCase->execute($request->all());
            if ($user instanceof User) {
                Auth::loginUsingId($user->id);
                return redirect()->route('editar-curriculo');
            }
            throw new \InvalidArgumentException('Erro ao registrar o currículo');
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            return back()->withErrors(['page' => $e->getMessage()]);
        } catch (\Throwable $e) {
            return back()->withErrors(['page' => $e->getMessage()]);
        }
    }
}
