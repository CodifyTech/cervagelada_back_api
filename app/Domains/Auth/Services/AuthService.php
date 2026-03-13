<?php

namespace App\Domains\Auth\Services;

use App\Domains\Auditoria\Services\AuditService;
use App\Domains\Auth\Models\User;
use App\Domains\Auth\Requests\ForgotPasswordRequest;
use App\Domains\Auth\Requests\LoginRequest;
use App\Domains\Auth\Requests\RegisterRequest;
use App\Domains\Auth\Requests\RegisterStoreRequest;
use App\Domains\Auth\Requests\ResetPasswordRequest;
use App\Domains\Loja\Models\Loja;
use App\Domains\Shared\Services\BaseService;
use App\Domains\Shared\Services\CepService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService extends BaseService
{
    private string $token;

    public function __construct(
        private User $user,
        private AuditService $auditService = new AuditService
    ) {}

    /**
     * @throws \Exception
     */
    public function login(LoginRequest $request)
    {
        $this->token = $this->validateCredentials($request);
        $this->user = Auth::user();

        $this->auditService->logLogin($this->user->id, $this->user->email);

        $ACL = collect($this->user->permissions())
            ->reduce(function ($ACL, $permission) {
                [$subject, $action] = explode(' ', $permission['slug']);
                $ACL['permissions'][] = [
                    'subject' => $subject,
                    'action' => $action,
                ];
                $ACL['subjects'][] = $action;

                return $ACL;
            }, []);

        return response()->json([
            'user' => $this->user,
            'authorization' => [
                'token' => $this->token,
                'type' => 'Bearer',
                'expires_in' => $this->getTTLInSeconds(),
                'subjects' => $ACL['subjects'],
                'permissions' => $ACL['permissions'],
            ],
        ]);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->user->create([
            'name' => $request->name,
            'email' => $request->email,
            'celular' => $request->celular,
            'password' => Hash::make($request->password),
            'termos' => $request->termos,
        ]);

        if (! empty($request->roles)) {
            $user->assignRole($request->roles);
        }

        $this->user->sendEmailVerificationNotification();

        $loggedIn = $this->login(new LoginRequest([
            'email' => $request->email,
            'password' => $request->password,
        ]));

        return response()->json($loggedIn->getData());
    }

    public function registerWithStore(RegisterStoreRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $lojaData = $request->only([
                'nome_fantasia', 'tipo_loja', 'latitude', 'longitude', 'raio_entrega_km',
                'tempo_entrega_min', 'tempo_entrega_max', 'aceite_automatico', 'pedido_minimo',
                'taxa_comissao', 'ativo', 'cep', 'logradouro', 'numero', 'complemento',
                'bairro', 'cidade', 'estado',
            ]);

            $coords = app(CepService::class)->geocode($lojaData);
            if ($coords['latitude'] && $coords['longitude']) {
                $lojaData['latitude'] = $coords['latitude'];
                $lojaData['longitude'] = $coords['longitude'];
            }

            $loja = Loja::create($lojaData);

            if ($request->has('horarios')) {
                foreach ($request->horarios as $horario) {
                    $loja->horarios()->create($horario);
                }
            }

            $user = $this->user->create([
                'name' => $request->name,
                'email' => $request->email,
                'celular' => $request->celular,
                'cpf' => $request->cpf,
                'password' => Hash::make($request->password),
                'termos' => $request->termos,
                'loja_id' => $loja->id,
            ]);

            $user->markEmailAsVerified();

            if (! empty($request->roles)) {
                $user->assignRole($request->roles);
            }

            $loggedIn = $this->login(new LoginRequest([
                'email' => $request->email,
                'password' => $request->password,
            ]));

            return response()->json($loggedIn->getData());
        });
    }

    public function forgotPassword(ForgotPasswordRequest $request): array
    {
        $message = [];
        $status = Password::sendResetLink(
            $request->only('email'),
        );

        if ($status == Password::RESET_THROTTLED) {
            $message['message'] = 'Tentativa de reinicialização acelerada.';
            $message['status'] = false;
        }
        if ($status == Password::INVALID_USER) {
            $message['message'] = 'Usuário não existe';
            $message['status'] = false;
        }

        return $message;
    }

    public function resetPassword(ResetPasswordRequest $request): array
    {
        $message = [];
        Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                $status = event(new PasswordReset($user));

                if ($status == 'passwords.reset') {
                    $message['message'] = 'Senha alterada com sucesso!';
                    $message['status'] = true;
                } else {
                    $message['message'] = 'Ocorreu um error ao alterar a senha!';
                    $message['status'] = false;
                }
            },
        );

        return $message;
    }

    public function profile(): JsonResponse
    {
        return response()->json(auth()->user());
    }

    public function updateProfile(): JsonResponse
    {
        $user = auth()->user();
        $data = request()->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,'.$user->id,
            'telefone' => 'nullable|string|max:20',
        ]);

        $user->update($data);

        return response()->json($user->fresh());
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(Auth::refresh());
    }

    public function logout()
    {
        $userId = auth()->id();
        try {
            Auth::logout();
            if ($userId) {
                $this->auditService->logLogout($userId);
            }
        } catch (\Exception $e) {
            // Se o token já expirou ou é inválido, ainda consideramos logout bem-sucedido
            Log::info('Logout realizado com token expirado/inválido: '.$e->getMessage());
        }
    }

    /**
     * Get the token array structure.
     *
     * @param  string  $token
     * @return JsonResponse
     */
    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->getTTLInSeconds(),
        ];
    }

    protected function validateCredentials(LoginRequest $request)
    {
        $token = Auth::attempt($request->only('email', 'password'));

        if (! $token) {
            $this->auditService->logLoginFailed($request->email);
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        if (! Auth::user()->ativo) {
            throw ValidationException::withMessages([
                'email' => ['O seu usuário não está ativo. Por favor, entre em contato com o suporte para solicitar a ativação da sua conta.'],
            ]);
        }

        return $token;
    }

    /**
     * Get TTL in seconds with fallback
     */
    private function getTTLInSeconds(): int
    {
        try {
            return JWTAuth::factory()->getTTL() * 60;
        } catch (\Exception $e) {
            // Fallback para config direto
            return config('jwt.ttl', 60) * 60;
        }
    }

    protected function deletePreviousAccessTokensOnLogin(User $credentials): void
    {
        if (config('cdf.delete_previous_access_tokens_on_login', false)) {
            $credentials->tokens()->delete();
        }
    }
}
