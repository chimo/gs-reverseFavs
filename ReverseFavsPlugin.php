<?php
/**
 * StatusNet - the distributed open-source microblogging tool
 * Copyright (C) 2010, StatusNet, Inc.
 *
 * A plugin to enable social-bookmarking functionality
 *
 * PHP version 5
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category  SocialBookmark
 * @package   StatusNet
 * @author    Evan Prodromou <evan@status.net>
 * @copyright 2010 StatusNet, Inc.
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html AGPL 3.0
 * @link      http://status.net/
 */

if (!defined('STATUSNET')) {
    exit(1);
}

/**
 * ReverseFavs plugin main class
 *
 * @category  Favorites
 * @package   StatusNet
 * @author    Stephane Berube <chimo@chromic.org>
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html AGPL 3.0
 * @link      https://github.com/chimo/ReverseFavs
 */
class ReverseFavsPlugin extends Plugin
{
    const VERSION = '0.1';

    /**
     * Load related modules when needed
     *
     * @param string $cls Name of the class to be loaded
     *
     * @return boolean hook value; true means continue processing, false means stop.
     */
    function onAutoload($cls)
    {
        $dir = dirname(__FILE__);
        switch ($cls)
        {
        case 'ReversefavsAction':
            include_once $dir . '/' . strtolower(mb_substr($cls, 0, -6)) . '.php';
            return false;
        default:
            return true;
        }
    }

    /**
     * Map URLs to actions
     *
     * @param Net_URL_Mapper $m path-to-action mapper
     *
     * @return boolean hook value; true means continue processing, false means stop.
     */
    function onRouterInitialized($m)
    {
        if (common_config('singleuser', 'enabled')) {
            $nickname = User::singleUserNickname();
            $m->connect('reversefavs',
                        array('action' => 'reversefavs'),
                        array('nickname' => $nickname)); // FIXME: useless
        } else {
            $m->connect(':nickname/reversefavs',
                        array('action' => 'reversefavs'),
                        array('nickname' => Nickname::DISPLAY_FMT));
        }

        return true;
    }

    /**
     * Modify the default menu to link to our custom action
     *
     * Using event handlers, it's possible to modify the default UI for pages
     * almost without limit. In this method, we add a menu item to the default
     * primary menu for the interface to link to our action.
     *
     * The Action class provides a rich set of events to hook, as well as output
     * methods.
     *
     * @param Action $action The current action handler. Use this to
     *                       do any output.
     *
     * @return boolean hook value; true means continue processing, false means stop.
     *
     * @see Action
     */
    function onEndPersonalGroupNav($action)
    {
        $this->user = common_current_user();

        if (!$this->user) {
            // TRANS: Client error displayed when trying to display rfavs for a non-existing user.
            $this->clientError(_('No such user.'));
            return false;
        }

        $action->menuItem(common_local_url('reversefavs', array('nickname' => $nickname)),
                          // TRANS: Menu item in sample plugin.
                          _m('Reverse Favs'),
                          // TRANS: Menu item title in sample plugin.
                          _m('A list of your notices, favored by others'), false, 'nav_timeline_rfavs');
        return true;
    }

    /**
     * Plugin version data
     *
     * @param array &$versions array of version data
     *
     * @return value
     */
    function onPluginVersion(&$versions)
    {
        $versions[] = array('name' => 'ReverseFavs',
                            'version' => self::VERSION,
                            'author' => 'Stephane Berube',
                            'homepage' => 'https://github.com/chimo/ReverseFavs',
                            'description' =>
                            // TRANS: Plugin description.
                            _m('Simple extension for displaying your notices that have been favored by others.'));
        return true;
    }
}
