<?php

if (!defined('GNUSOCIAL')) {
    exit(1);
}

class ReverseFavsPlugin extends Plugin
{
    const VERSION = '0.1';

    /**
     * Map URLs to actions
     *
     * @param Net_URL_Mapper $m path-to-action mapper
     *
     * @return boolean hook value; true means continue processing, false means stop.
     */
    function onRouterInitialized($m)
    {
        $m->connect(':nickname/reversefavs',
                    array('action' => 'reversefavs'),
                    array('nickname' => Nickname::DISPLAY_FMT));

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

        $action->menuItem(common_local_url('reversefavs', array('nickname' => $this->user->nickname)),
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
    function onPluginVersion(array &$versions)
    {
        $versions[] = array('name' => 'ReverseFavs',
                            'version' => self::VERSION,
                            'author' => 'chimo',
                            'homepage' => 'https://github.com/chimo/gs-reverseFavs',
                            'description' =>
                            // TRANS: Plugin description.
                            _m('Simple extension for displaying your notices that have been favored by others.'));
        return true;
    }
}
