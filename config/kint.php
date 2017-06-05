<?php

/*
|--------------------------------------------------------------------------
| Kint Configuration Options
|--------------------------------------------------------------------------
|
| See Kint documentation for full details on what each option does.
|
*/

return [

	/*
	 * If set to false, Kint will become silent
	 */ 
	'enabled' => true, // I suggest replacing true with env('APP_DEBUG'), 

	'displayCalledFrom' => true,
	
	'fileLinkFormat' => ini_get('xdebug.file_link_format'),

	/*
	 * The file paths displayed within debug traces
	*/
	'appRootDirs' => array(
		base_path()=>'.', // just display a period at application root
// 		base_path()=>base_path(), // display the full path
	),

	'maxStrLength' => 80,
	
	'maxLevels' => 5,

	'theme' => 'original',
		
	'expandedByDefault'=>false,
		
	'cliDetection'=>true,

	'cliColors'=>true,

	/*
	 * Allows you to use these in blade templates:
	 * @d($var) @ddd($var) @sd($var) @s($var) @dd($var)
	 */
	'blade_directives' => true,
	
];
