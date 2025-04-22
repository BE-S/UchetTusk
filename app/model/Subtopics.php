<?php

namespace App\Model;

use Libs\Database\QueryBilder;

class Subtopics extends QueryBilder
{
	public $topic_id;
	public $name;
    public $text;

	public function __construct()
	{
		parent::__construct();
	}
}