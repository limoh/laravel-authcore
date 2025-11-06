<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Laravel\Passport\TokenRepository;

class IntrospectController extends Controller
{
    protected $tokens;

    public function __construct(TokenRepository $tokens)
    {
        $this->tokens = $tokens;
    }

    public function introspect(Request $request)
    {
        $tokenId = $request->input('token');
        $token = $this->tokens->find($tokenId);

        if (!$token || $token->revoked) {
            return response()->json(['active' => false]);
        }

        return response()->json([
            'active' => true,
            'user' => $token->user,
            'client_id' => $token->client_id,
            'scopes' => $token->scopes,
            'expires_at' => $token->expires_at,
        ]);
    }
}
