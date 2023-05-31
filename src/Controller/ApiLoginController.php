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
use Symfony\Component\Validator\Constraints\Json;

class ApiLoginController extends AbstractController
{
    public function __construct(
        protected UserPasswordHasherInterface $passwordEncoder,
        protected JWTTokenManagerInterface    $tokenManager,
        protected UserRepository              $repository)
    {
    }

    #[Route('/', name: 'app')]
    public function index()
    {
        $welcome = [
            "Welcome to e-commerce-app",
            "To get started visit the URL: https://github.com/osniel993/e-commerce-app "
        ];

        return new JsonResponse($welcome);
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
