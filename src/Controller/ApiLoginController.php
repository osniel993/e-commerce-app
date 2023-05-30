<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ApiLoginController extends AbstractController
{
    public function __construct(
        protected UserPasswordHasherInterface $passwordEncoder,
        protected JWTTokenManagerInterface    $tokenManager,
        protected UserRepository              $repository)
    {
    }

    #[Route('/login', name: 'app_login', methods: 'post')]
    public function login(Request $request): JsonResponse
    {
        $username = $request->server->get('PHP_AUTH_USER');
        $password = $request->server->get('PHP_AUTH_PW');

        $user = $this->repository->findOneBy(['username' => $username]);

        if (!$user || !$this->passwordEncoder->isPasswordValid($user, $password)) {
            return new JsonResponse('Invalid credentials', Response::HTTP_UNAUTHORIZED);
        }

        $token = $this->tokenManager->create($user);

        return new JsonResponse([
            'token' => $token,
            'token_type' => "Bearer"
        ]);
    }
}
