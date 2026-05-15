<?php
/**
 * A minimal Craft plugin to provide Google OAuth login
 *
 * @author     Leo Leoncio
 * @see        https://github.com/leowebguy
 * @copyright  Copyright (c) 2026, leowebguy
 * @license    Craft
 */

namespace leowebguy\googleoauth\models;

use craft\base\Model;

class AuthModel extends Model
{
    public string $active = '0';
    public string $disableLogin = '0';
    public string $clientID = '$GOOGLE_CLIENT_ID';
    public string $clientSecret = '$GOOGLE_CLIENT_SECRET';
}
