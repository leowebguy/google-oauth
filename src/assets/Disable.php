<?php
/**
 * A minimal Craft plugin to provide Google OAuth login
 *
 * @author     Leo Leoncio
 * @see        https://github.com/leowebguy
 * @copyright  Copyright (c) 2026, leowebguy
 * @license    MIT
 */

namespace leowebguy\googleoauth\assets;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class Disable extends AssetBundle
{
    public function init(): void
    {
        $settings = Craft::$app->plugins->getPlugin('google-oauth')->getSettings();

        if (!$settings['disableLogin'])
            return;

        $this->sourcePath = '@leowebguy/googleoauth/assets';
        $this->depends = [CpAsset::class];

        $this->js = ['disable.js'];

        $this->jsOptions = [
            'defer' => true,
        ];

        parent::init();
    }
}
