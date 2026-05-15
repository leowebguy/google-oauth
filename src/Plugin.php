<?php
/**
 * A minimal Craft plugin to provide Google OAuth login
 *
 * @author     Leo Leoncio
 * @see        https://github.com/leowebguy
 * @copyright  Copyright (c) 2026, leowebguy
 * @license    Craft
 */

namespace leowebguy\googleoauth;

use Craft;
use craft\base\Model;
use craft\base\Plugin as BasePlugin;
use craft\events\RegisterUrlRulesEvent;
use craft\web\UrlManager;
use craft\web\View;
use leowebguy\googleoauth\assets\Disable;
use leowebguy\googleoauth\assets\Google;
use leowebguy\googleoauth\models\AuthModel;
use leowebguy\googleoauth\services\AuthService;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\Event;
use yii\base\Exception as BaseException;

class Plugin extends BasePlugin
{
    public bool $hasCpSection = false;

    public bool $hasCpSettings = true;

    public function init(): void
    {
        parent::init();

        if (!$this->isInstalled) {
            return;
        }

        $this->setComponents([
            'oauthService' => AuthService::class
        ]);

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $e) {
                $e->rules['oauth/g/url'] = 'google-oauth/auth/google-url';
                $e->rules['oauth/g/auth'] = 'google-oauth/auth/google-auth';
            }
        );

        Event::on(
            View::class,
            View::EVENT_BEFORE_RENDER_TEMPLATE,
            function () {
                $view = Craft::$app->getView();
                $view->registerAssetBundle(Google::class);
                $view->registerAssetBundle(Disable::class);
            }
        );

        // log info
        Craft::info(
            'Google OAuth plugin loaded',
            __METHOD__
        );
    }

    /**
     * @return Model|null
     */
    protected function createSettingsModel(): ?Model
    {
        return new AuthModel;
    }

    /**
     * @return string|null
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws BaseException
     */
    protected function settingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('google-oauth/settings', [
            'settings' => $this->getSettings(),
        ]);
    }
}
