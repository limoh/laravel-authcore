<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Passport\ClientRepository;
use App\Models\UserConsent;
use App\Services\AuditService;

class ConsentController extends Controller
{
    public function show(Request $request)
    {
        // Passport puts the authorize request parameters on the session (if using built-in)
        $clientId = $request->query('client_id');
        $scopes = explode(' ', $request->query('scope', ''));

        // load client details (oauth_clients or app_clients)
        $client = \DB::table('oauth_clients')->where('id', $clientId)->first();

        return view('auth.consent', [
            'client' => $client,
            'scopes' => $scopes,
            'request' => $request->all()
        ]);
    }

    public function approve(Request $request)
    {
        $user = $request->user();
        $clientId = $request->input('client_id');
        $grantedScopes = $request->input('scopes', []);

        // persist consent
        UserConsent::updateOrCreate(
            ['user_id' => $user->id, 'client_id' => $clientId],
            ['scopes' => $grantedScopes, 'granted_at' => now()]
        );

        AuditService::log($user, 'consent.granted', ['client_id' => $clientId, 'scopes' => $grantedScopes]);

        // continue with Passport authorization flow
        // If you're using Passport's authorize flow, call the controller that approves the authorization.
        // Simplest approach: forward to Passport's Authorize route with 'approve' param (varies by customization).
        return app(\Laravel\Passport\Http\Controllers\ApproveAuthorizationController::class)->approve($request);
    }

    public function deny(Request $request)
    {
        $user = $request->user();
        $clientId = $request->input('client_id');

        AuditService::log($user, 'consent.denied', ['client_id' => $clientId]);

        return app(\Laravel\Passport\Http\Controllers\DenyAuthorizationController::class)->deny($request);
    }
}
