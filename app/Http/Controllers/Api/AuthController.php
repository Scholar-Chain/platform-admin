<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Api\Auth\EditRequest;
use App\Repositories\Eloquent\UserRepository;
use App\Http\Requests\Api\Auth\RegisterRequest;

class AuthController extends Controller
{
    private $userModel;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(UserRepository $userModel)
    {
        $this->userModel = $userModel;
        // $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function edit(EditRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            $user = $this->userModel->update($id, $data);
            DB::commit();
            return response()->json($user);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollback();
            return response()->json([
                'errors' => 'Data not found',
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            report($e);
            DB::rollback();
            return response()->json([
                'errors' => 'Proses data gagal, silahkan coba lagi',
            ], $e->getCode() == 0 ? 500 : ($e->getCode() != 23000 ? $e->getCode() : 500));
        }
    }

    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            $user = User::create([
                "name" => $data['given_name'],
                "email" => $data['email'],
                "password" => bcrypt($data['password']),
            ])->author()->create([
                'given_name' => $data['given_name'],
                'family_name' => $data['family_name'],
                'affiliation' => $data['affiliation']
            ]);

            DB::commit();
            $token = JWTAuth::fromUser($user);
            return $this->respondWithToken($token);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollback();
            return response()->json([
                'errors' => 'Data not found',
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            report($e);
            DB::rollback();
            return response()->json([
                'errors' => 'Proses data gagal, silahkan coba lagi',
            ], $e->getCode() == 0 ? 500 : ($e->getCode() != 23000 ?: 500));
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth('api')->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
