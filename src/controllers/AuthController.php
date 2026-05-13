<?php
/**
 * A minimal Craft plugin to provide Google OAuth login
 *
 * @author     Leo Leoncio
 * @see        https://github.com/leowebguy
 * @copyright  Copyright (c) 2026, leowebguy
 * @license    MIT
 */

namespace leowebguy\googleoauth\controllers;

use Craft;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use Google\Service\Exception as GoogleException;
use leowebguy\googleoauth\Plugin;
use yii\base\Exception;
use yii\base\ExitException;
use yii\base\InvalidRouteException;
use yii\web\Response;

class AuthController extends Controller
{
    public $enableCsrfValidation = false;

    protected int|bool|array $allowAnonymous = [
        'google-url',
        'google-auth',
    ];

    /**
     * @return Response
     * @throws Exception
     */
    public function actionGoogleUrl(): Response
    {
        $response = Craft::$app->getResponse();

        $response->headers->set('Access-Control-Allow-Methods', 'GET, OPTIONS');

        $url = Plugin::getInstance()->oauthService->url();

        return $this->asJson([
            'url' => $url
        ]);
    }

    /**
     * @return void
     * @throws Exception
     * @throws ExitException
     * @throws GoogleException
     * @throws InvalidRouteException
     */
    public function actionGoogleAuth(): void
    {
        $params = Craft::$app->request->getQueryParams();

        if (!$params['code']) {
            Craft::error('Missing OAuth Code', __METHOD__);
            Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('login?error=Missing OAuth Code'));
        }

        Plugin::getInstance()->oauthService->auth($params['code']);
    }
}
