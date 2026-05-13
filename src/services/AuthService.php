<?php
/**
 * A minimal Craft plugin to provide Google OAuth login
 *
 * @author     Leo Leoncio
 * @see        https://github.com/leowebguy
 * @copyright  Copyright (c) 2026, leowebguy
 * @license    MIT
 */

namespace leowebguy\googleoauth\services;

use Craft;
use craft\base\Component;
use craft\helpers\App;
use craft\helpers\StringHelper;
use craft\helpers\UrlHelper;
use Google\Client;
use Google\Service\Exception as GoogleException;
use Google\Service\Oauth2;
use yii\base\Exception;
use yii\base\ExitException;
use yii\base\InvalidRouteException;

class AuthService extends Component
{
    /**
     * @return string
     * @throws Exception
     */
    public function url(): string
    {
        $client = self::googleClient();

        return $client->createAuthUrl();
    }

    /**
     * @param $code
     * @return void
     * @throws Exception
     * @throws ExitException
     * @throws GoogleException
     * @throws InvalidRouteException
     */
    public function auth($code): void
    {
        $client = self::googleClient();

        $token = $client->fetchAccessTokenWithAuthCode($code);
        $client->setAccessToken($token);

        // Get user profile info
        $oauth = new Oauth2($client);
        $user_info = $oauth->userinfo->get();

        // Access user details
        $email = $user_info->email;

        // Now log the user into your application session
        $user = Craft::$app->getUsers()->getUserByUsernameOrEmail($email);

        if (!$user)
            Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('login?error=User does not have an account in this application'));

        Craft::$app->getUser()->loginByUserId($user->id);
        Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('dashboard'));
        Craft::$app->end();
    }

    /**
     * @return Client
     * @throws Exception
     */
    private function googleClient(): Client
    {
        $settings = Craft::$app->plugins->getPlugin('google-oauth')->getSettings();

        if (!$settings['clientID'] || !$settings['clientSecret']) {
            Craft::error('Missing Client ID or Secret', __METHOD__);
            Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('login?error=Missing Client ID or Secret'));
        }

        $client = new Client();
        $client->setClientId(App::parseEnv($settings['clientID']));
        $client->setClientSecret(App::parseEnv($settings['clientSecret']));
        $client->setRedirectUri(StringHelper::trimRight(UrlHelper::baseCpUrl(), '/') . '/oauth/g/auth');
        $client->addScope("email");

        return $client;
    }
}
