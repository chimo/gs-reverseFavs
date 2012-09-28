<?php
// Needed for 'ThreadedNoticeListFavesItem' class
require_once('./lib/threadednoticelist.php');

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

    function showEnd()
    {
        $max = $this->initialItems();

        if (Event::handle('StartShowThreadedNoticeTail', array($this, $this->notice, null))) {
            $this->out->elementStart('ul', 'notices threaded-replies xoxo');

            // People who favored this notice
            $item = new ThreadedNoticeListFavesItem($this->notice, $this->out);
            $hasFaves = $item->show();

            $this->out->elementEnd('ul');
            Event::handle('EndShowThreadedNoticeTail', array($this, $this->notice, $notices));
        }

        parent::showEnd();
    }
}
?>
