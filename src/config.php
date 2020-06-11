<?php declare(strict_types=1);

return [

	/*
	 | The application's url.
	 |
	 | It is not recommended to rely on $_SERVER['SERVER_NAME']
	 | or $_SERVER['HOST'], as they could be spoofed.
	*/
	
	'app_url' => 'https://image.microapi.dev/',

	/*
	 | The maximum file size to be uploaded.
	 |
	 | We set a limit on the size of image in bytes to be uploaded for procession.
	 | By default, the size limit is 3MB. You may wish to increase in accordance
	 | to your server capacity.
	*/

	 'max_filesize' => 5000000,

];
