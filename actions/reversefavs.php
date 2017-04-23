<?php

if (!defined('GNUSOCIAL')) {
    exit(1);
}

class ReverseFavsAction extends Action
{
    var $user = null;
    var $gc   = null;

    /**
     * Take arguments for running
     *
     * This method is called first, and it lets the action class get
     * all its arguments and validate them. It's also the time
     * to fetch any relevant data from the database.
     *
     * Action classes should run parent::prepare($args) as the first
     * line of this method to make sure the default argument-processing
     * happens.
     *
     * @param array $args $_REQUEST args
     *
     * @return boolean success flag
     */
    function prepare(array $args = array())
    {
        parent::prepare($args);

        if (common_config('singleuser', 'enabled')) {
            $nickname = User::singleUserNickname();
        } else {
            // PHP 5.4
            // $nickname = $this->returnToArgs()[1]['nickname'];

            // PHP < 5.4
            $nickname = $this->returnToArgs();
            $nickname = $nickname[1]['nickname'];
        }

        $this->user = User::getKV('nickname', $nickname);

        if (!$this->user) {
            // TRANS: Client error displayed when trying to display rfavs for a non-existing user.
            $this->clientError(_('No such user.'));
            return false;
        }

        $this->page = ($this->arg('page')) ? ($this->arg('page')+0) : 1;

        $stream = new ReverseFavsNoticeStream($this->user->id, true);
        $this->notices = $stream->getNotices(($this->page-1)*NOTICES_PER_PAGE,
                                                NOTICES_PER_PAGE + 1);

        if($this->page > 1 && $this->notices->N == 0) {
            throw new ClientException(_('No such page.'), 404);
        }

        return true;
    }

    /**
     * Handle request
     *
     * This is the main method for handling a request. Note that
     * most preparation should be done in the prepare() method;
     * by the time handle() is called the action should be
     * more or less ready to go.
     *
     * @param array $args $_REQUEST args; handled in prepare()
     *
     * @return void
     */
    function handle()
    {
        parent::handle();
        $this->showPage();
    }

    /**
     * Title of this page
     *
     * Override this method to show a custom title.
     *
     * @return string Title of the page
     */
    function title()
    {

        if (empty($this->user)) {
            // TRANS: Page title for sample plugin.
            return _m('Log in');
        } else {
            // TRANS: Page title for sample plugin. %s is a user nickname.
            return sprintf(_m('%s\'s notices, favored by others'), $this->user->nickname);
        }
    }

    /**
     * Show content in the content area
     *
     * The default StatusNet page has a lot of decorations: menus,
     * logos, tabs, all that jazz. This method is used to show
     * content in the content area of the page; it's the main
     * thing you want to overload.
     *
     * This method also demonstrates use of a plural localized string.
     *
     * @return void
     */
    function showContent()
    {

        $nl = new ReverseFavsNoticeList($this->notices, $this);

        $cnt = $nl->show();

        if ($cnt == 0) {
            $this->showEmptyList();
        }

        $this->pagination($this->page > 1,
                $cnt > NOTICES_PER_PAGE,
                $this->page, 'reversefavs',
                array('nickname' => $this->user->nickname));
    }

    function showEmptyList() {
        $message = sprintf(_('This is %1$s\'s reverse favorites stream, but nobody favored %1$s\'s notices yet.'), $this->user->nickname) . ' ';

        $this->elementStart('div', 'guide');
        $this->raw(common_markup_to_html($message));
        $this->elementEnd('div');
    }

    /**
     * Return true if read only.
     *
     * Some actions only read from the database; others read and write.
     * The simple database load-balancer built into StatusNet will
     * direct read-only actions to database mirrors (if they are configured),
     * and read-write actions to the master database.
     *
     * This defaults to false to avoid data integrity issues, but you
     * should make sure to overload it for performance gains.
     *
     * @param array $args other arguments, if RO/RW status depends on them.
     *
     * @return boolean is read only action?
     */
    function isReadOnly($args)
    {
        return true;
    }
}
