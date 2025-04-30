<?php

namespace App\Services;

use Google\Auth\OAuth2;
use Google\Auth\CredentialsLoader;

class DialogflowService
{
    public static function getAccessToken()
    {
        $jsonKey = json_decode(file_get_contents(base_path(env('DIALOGFLOW_CREDENTIALS_PATH'))), true);

        $oauth = new OAuth2([
            'audience' => CredentialsLoader::TOKEN_CREDENTIAL_URI,
            'issuer' => $jsonKey['client_email'],
            'signingAlgorithm' => 'RS256',
            'signingKey' => $jsonKey['private_key'],
            'tokenCredentialUri' => CredentialsLoader::TOKEN_CREDENTIAL_URI,
            'scope' => ['https://www.googleapis.com/auth/dialogflow']
        ]);

        $token = $oauth->fetchAuthToken();

        return $token['access_token'] ?? null;
    }
}
