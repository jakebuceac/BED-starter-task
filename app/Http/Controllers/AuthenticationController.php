<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginStoreRequest;
use App\Http\Requests\UserStoreRequest;
use App\Http\Resources\UserResource;
use App\Jobs\SendEmailJob;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Throwable;

class AuthenticationController extends Controller
{

    /**
     * Sets up an account for the user that is saved to the database
     *
     * @param UserStoreRequest $request
     * @return UserResource
     * @throws Throwable
     */
    public function register(UserStoreRequest $request): UserResource
    {
        $newUser = new User();

        $newUser->name = $request->name;
        $newUser->email = $request->email;
        $newUser->password = Hash::make($request->password);
        $newUser->saveOrFail();

        return UserResource::make($newUser);
    }

    /**
     * Validates that the user has an account and creates a token for that user if they are
     *
     * @param LoginStoreRequest $request
     * @return mixed
     * @throws ValidationException
     */
    public function login(LoginStoreRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials))
        {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        $user = Auth::user();

        // creates token for that user
        $token = $user->createToken($request->device_name)->plainTextToken;

        // calls the SendEmailJob class to queue sending the email to the user
        $job = (new SendEmailJob($user, $request->ip()));

        // dispatches the job to the queue
        $this->dispatch($job);

        return new JsonResponse([
            'data' => [
                'token' => $token
            ]
        ]);
    }
}
