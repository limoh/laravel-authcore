<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Support\Facades\DB;

class RefreshTokenController extends AccessTokenController
{
    protected $tokenRepository;
    protected $refreshTokenRepository;

    public function __construct(TokenRepository $tokenRepository, RefreshTokenRepository $refreshTokenRepository)
    {
        $this->tokenRepository = $tokenRepository;
        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    public function issueRotatedToken(ServerRequestInterface $request)
    {
        return DB::transaction(function () use ($request) {
            // Step 1: Issue new access + refresh token
            $response = parent::issueToken($request);
            $payload = json_decode($response->getBody()->__toString(), true);

            // Step 2: Identify old refresh token from request
            $oldRefreshToken = request('refresh_token');

            // Step 3: Revoke old refresh token
            if ($oldRefreshToken) {
                $this->refreshTokenRepository->revokeRefreshToken($oldRefreshToken);
            }

            // Step 4: Return new tokens
            return response()->json($payload);
        });
    }
}
