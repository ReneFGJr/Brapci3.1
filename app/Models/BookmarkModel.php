<?php

namespace App\Models;

use CodeIgniter\Model;

class BookmarkModel extends Model
{
    protected $DBGroup = 'bookmarks';
    protected $table = 'bookmarks';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'url', 'folder', 'date_added', 'favicon', 'folder_id', 'clicks','click_last'];
}
