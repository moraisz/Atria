<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use Atria\Http\AbstractClasses\Controller;
use Atria\Modules\Auth\AuthManager;
use Atria\Modules\Auth\Exceptions\AuthenticationException;
use Atria\Modules\Auth\Data\AuthenticatedPrincipal;
use Atria\Http\Response;

class AuthController extends Controller
{
    public function __construct(
        private AuthManager $authManager,
    ) {}

    public function login(): Response
    {
        return $this->renderView('pages/auth', [
            'title' => 'Login',
            'type' => 'login',
            'error' => $this->pullError(),
        ]);
    }

    public function loginUser(): Response
    {
        $deviceInfo = $this->request->getHeader('User-Agent') ?? 'unknown';
        $result = $this->authManager->attempt(
            $this->request->bodyString('email'),
            $this->request->bodyString('password'),
            $deviceInfo,
        );

        $this->authManager->attachCookies($this->response, $result);

        return $this->redirect($this->authManager->redirectAuthenticatedTo());
    }

    public function register(): Response
    {
        return $this->renderView('pages/auth', [
            'title' => 'Register',
            'type' => 'register',
            'error' => $this->pullError(),
        ]);
    }

    public function registerUser(): Response
    {
        $user = User::register(
            $this->request->bodyString('name'),
            $this->request->bodyString('email'),
            $this->request->bodyString('password'),
        );
        $userId = $user['id'] ?? null;
        $email = $user['email'] ?? null;

        if (!is_numeric($userId) || !is_string($email) || $email === '') {
            throw new \RuntimeException('Registered user is invalid.');
        }

        $deviceInfo = $this->request->getHeader('User-Agent') ?? 'unknown';
        $tokens = $this->authManager->issuePairForUser(
            new AuthenticatedPrincipal(
                (int) $userId,
                $email,
                is_string($user['name'] ?? null) ? $user['name'] : null,
            ),
            $deviceInfo,
        );
        $this->authManager->attachCookies($this->response, $tokens);

        return $this->redirect($this->authManager->redirectAuthenticatedTo());
    }

    public function refresh(): Response
    {
        $refreshToken = $this->request->getCookie($this->authManager->refreshCookieName());

        if ($refreshToken === null) {
            return $this->jsonResponse(['error' => 'Refresh token not found'], 401);
        }

        $deviceInfo = $this->request->getHeader('User-Agent') ?? 'unknown';

        try {
            $tokens = $this->authManager->refresh($refreshToken, $deviceInfo);
        } catch (AuthenticationException $e) {
            return $this->jsonResponse(['error' => $e->getMessage()], 401);
        }

        $this->authManager->attachCookies($this->response, $tokens);

        return $this->jsonResponse(['refreshed' => true]);
    }

    public function logout(): Response
    {
        $this->authManager->logout($this->request->getCookie($this->authManager->refreshCookieName()));
        $this->authManager->clearCookies($this->response);

        return $this->redirect($this->authManager->redirectGuestTo());
    }

    private function pullError(): ?string
    {
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);

        return is_string($error) ? $error : null;
    }
}
