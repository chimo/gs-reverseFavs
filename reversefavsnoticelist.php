<?php
class ReverseFavsNoticeList extends NoticeList
{
    protected $userProfile;

    function __construct($notice, $out=null, $profile=-1)
    {
        parent::__construct($notice, $out);
        if (is_int($profile) && $profile == -1) {
            $profile = Profile::current();
        }
        $this->userProfile = $profile;
    }

    function show()
    {
        $this->out->elementStart('div', array('id' =>'notices_primary'));

        $this->out->element('h2', null, _m('HEADER','Notices'));
        $this->out->elementStart('ol', array('class' => 'notices threaded-notices xoxo'));

        $notices = $this->notice->fetchAll();
        $total = count($notices);
        $notices = array_slice($notices, 0, NOTICES_PER_PAGE);

        foreach ($notices as $notice) {
            try {
                $item = $this->newListItem($notice);
                $item->show();
            } catch (Exception $e) {
                // we log exceptions and continue
                common_log(LOG_ERR, $e->getMessage());
                continue;
            }
        }

        $this->out->elementEnd('ol');
        $this->out->elementEnd('div');

        return $total;
    }

    function newListItem($notice)
    {
        return new ReverseFavsNoticeListItem($notice, $this->out, $this->userProfile);
    }
}

class ReverseFavsNoticeListItem extends NoticeListItem
{
    protected $userProfile = null;

    function __construct($notice, $out=null, $profile=null)
    {
        parent::__construct($notice, $out);
        $this->userProfile = $profile;
    }

    // Same as ./lib/threadednoticelist
    function initialItems() {
        return 3;
    }

    function showEnd()
    {
        // $notices = $this->notice->fetchAll();
        // $max = $this->initialItems();

        // All people who faved this notice
        $item = new ReverseFavsActors($this->notice, $this->out);
        $item->show();

        parent::showEnd();
    }
}

class ReverseFavsActors extends NoticeListActorsItem
{
    // From /plugins/Favorite/lib/threadednoticelistfavesitem.php
    function getProfiles()
    {
        $faves = Fave::byNotice($this->notice);
        $profiles = array();
        foreach ($faves as $fave) {
            $profiles[] = $fave->user_id;
        }
        return $profiles;
    }

    function magicList($items)
    {
        return parent::magicList($items);
    }

    function getListMessage($count, $you)
    {
        return sprintf(_m('%%s like this'));
    }

    function showStart()
    {
        $this->out->elementStart('div', array('class' => 'notice-faves'));
    }

    function showEnd()
    {
        $this->out->elementEnd('div');
    }
}

?>
