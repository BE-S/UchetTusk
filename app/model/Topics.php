<?php

namespace App\Model;

use Libs\Database\QueryBilder;

class Topics extends QueryBilder
{
	public $id;
	public $name;

	public function __construct()
	{
		parent::__construct();
	}
}