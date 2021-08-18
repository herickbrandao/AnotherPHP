<?php

/**
 * 
 */
class myController
{
	public function index(): void
	{
		echo file_get_contents('../system/200.html');
	}

	public function anotherPageExample(): void
	{
		echo 'Hello user number '. $_GET['ARGS']['id'];
	}
}