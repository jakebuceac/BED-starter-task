<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Jobs\SendEmailJob;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Throwable;

class AuthenticationController extends Controller
{

    /**
     * Sets up an account for the user that is saved to the database
     *
     * @param UserStoreRequest $request
     * @return User
     * @throws Throwable
     */
    public function register(UserStoreRequest $request): User
    {
        // creates new user object
        $newUser = new User();

        // if the account does not exist data parsed in will be saved to the database
        $newUser->name = $request->name;
        $newUser->email = $request->email;
        $newUser->password = Hash::make($request->password);
        $newUser->saveOrFail();


        // returns the user information parsed in
        return $newUser;
    }

    /**
     * Validates that the user has an account and creates a token for that user if they are
     *
     * @param Request $request
     * @return mixed
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        // validates that these fields have been filled
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        // gets the user by the email parsed in
        $user = User::where('email', $request->email)->first();

        // if it cannot find the user or the hash does not match the one saved on the database it will throw an exception
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // creates and returns the token for that user
        $token = $user->createToken($request->device_name)->plainTextToken;

        // calls the SendEmailJob class to queue sending the email to the user
        $job = (new SendEmailJob($user, $request->ip()));

        // dispatches the job to the queue
        $this->dispatch($job);

        // returns the created api token
        return new JsonResponse([
            'data' => [
                'token' => $token
            ]
        ]);
    }
}
