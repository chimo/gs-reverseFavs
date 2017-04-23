<?php

if (!defined('GNUSOCIAL')) {
    exit(1);
}

class RawReverseFavsNoticeStream extends NoticeStream
{
    protected $user_id;
    protected $own;

    function __construct($user_id, $own)
    {
        $this->user_id = $user_id;
        $this->own     = $own;
    }

    function getNoticeIds($offset, $limit, $since_id, $max_id)
    {
        $notice = new Notice();
        $qry = null;

        $qry =  'SELECT notice.* FROM notice ';
        $qry .= 'INNER JOIN fave ON fave.notice_id = notice.id ';
        $qry .= 'WHERE fave.user_id != ' . $this->user_id . ' ';    // Ignore notices current user fav'ed
        $qry .= 'AND notice.profile_id = ' . $this->user_id . ' ';  // Only get current user's notices

        if ($since_id != 0) {
            $qry .= 'AND notice.id > ' . $since_id . ' ';
        }

        if ($max_id != 0) {
            $qry .= 'AND notice.id <= ' . $max_id . ' ';
        }

        // NOTE: we sort by fave time, not by notice time!
        $qry .= 'ORDER BY created DESC ';
        if (!is_null($offset)) {
            $qry .= "LIMIT $limit OFFSET $offset";
        }

        $notice->query($qry);
        $ids = array();
        while ($notice->fetch()) {
            $ids[] = $notice->id;
        }

        $notice->free();
        unset($notice);
        return $ids;
    }
}

class ReverseFavsNoticeStream extends ScopingNoticeStream
{
    function __construct($user_id, $own, $profile = -1)
    {
        $stream = new RawReverseFavsNoticeStream($user_id, $own);

        if ($own) {
            $key = 'reversefavs:ids_by_user_own:'.$user_id;
        } else {
            $key = 'reversefavs:ids_by_user:'.$user_id;
        }

        if (is_int($profile) && $profile == -1) {
            $profile = Profile::current();
        }

        parent::__construct(new CachingNoticeStream($stream, $key),
                            $profile);
    }
}

