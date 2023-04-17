<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Carabiner 1.45 configuration file.
* CodeIgniter-library for Asset Management
*/

/*
|--------------------------------------------------------------------------
| Script Directory
|--------------------------------------------------------------------------
|
| Path to the script directory.  Relative to the CI front controller.
|
*/

$config['script_dir'] = 'js/';


/*
|--------------------------------------------------------------------------
| Style Directory
|--------------------------------------------------------------------------
|
| Path to the style directory.  Relative to the CI front controller
|
*/

$config['style_dir'] = 'css/';

/*
|--------------------------------------------------------------------------
| Cache Directory
|--------------------------------------------------------------------------
|
| Path to the cache directory. Must be writable. Relative to the CI 
| front controller.
|
*/

$config['cache_dir'] = 'cache/';




/*
* The following config values are not required.  See Libraries/Carabiner.php
* for more information.
*/



/*
|--------------------------------------------------------------------------
| Base URI
|--------------------------------------------------------------------------
|
|  Base uri of the site, like http://www.example.com/ Defaults to the CI 
|  config value for base_url.
|
*/

$config['base_uri'] = 'http://localhost/';


/*
|--------------------------------------------------------------------------
| Development Flag
|--------------------------------------------------------------------------
|
|  Flags whether your in a development environment or not. Defaults to FALSE.
|
*/

$config['dev'] = TRUE;


/*
|--------------------------------------------------------------------------
| Combine
|--------------------------------------------------------------------------
|
| Flags whether files should be combined. Defaults to TRUE.
|
*/

$config['combine'] = FALSE;


/*
|--------------------------------------------------------------------------
| Minify Javascript
|--------------------------------------------------------------------------
|
| Global flag for whether JS should be minified. Defaults to TRUE.
|
*/

$config['minify_js'] = FALSE;


/*
|--------------------------------------------------------------------------
| Minify CSS
|--------------------------------------------------------------------------
|
| Global flag for whether CSS should be minified. Defaults to TRUE.
|
*/

$config['minify_css'] = FALSE;

/*
|--------------------------------------------------------------------------
| Force cURL
|--------------------------------------------------------------------------
|
| Global flag for whether to force the use of cURL instead of file_get_contents()
| Defaults to FALSE.
|
*/

$config['force_curl'] = FALSE;


/*
|--------------------------------------------------------------------------
| Predifined Asset Groups
|--------------------------------------------------------------------------
|
| Any groups defined here will automatically be included.  Of course, they
| won't be displayed unless you explicity display them ( like this: $this->carabiner->display('jquery') )
| See docs for more.
| 
| Currently created groups:
|	> jQuery (latest in 1.xx version)
|	> jQuery UI (latest in 1.xx version)
|	> Ext Core (latest in 3.xx version)
|	> Chrome Frame (latest in 1.xx version)
|	> Prototype (latest in 1.x.x.x version)
|	> script.aculo.us (latest in 1.x.x version)
|	> Mootools (1.xx version)
|	> Dojo (latest in 1.xx version)
|	> SWFObject (latest in 2.xx version)
|	> YUI (latest core JS/CSS in 2.x.x version)
|
*/

// jQuery (latest, as of 1.xx)
$config['groups']['jquery'] = array(
	
	'js' => array(
	
		array('http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js', 'http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js', TRUE, FALSE)
	
	)
);

$config['groups']['homepage'] = array(
	
	'js' => array(
	
		array('jwplayer.js'),
		array('homepage.js')
	
	)
);

$config['groups']['admin_members'] = array(
	
	'js' => array(
	
		array('jquery-ui-1.7.3.custom.min.js'),
		array('admin/members.js')
	
	),	
	'css' => array(
		array('sunny/jquery-ui-1.7.3.custom.css')
	)
);

$config['groups']['dashboard'] = array(
	
	'js' => array(
	
		array('jquery-ui-1.7.3.custom.min.js'),
		array('dashboard.js')
	
	),	
	'css' => array(
		array('sunny/jquery-ui-1.7.3.custom.css')
	)
);

$config['groups']['stats'] = array(
	
	'js' => array(	
		array('highcharts.js'),		
		array('themes/grid.js'),
		array('stats.js')	
	
	)
);

$config['groups']['clients'] = array(
	
	'js' => array(
		array('jquery-ui-1.7.3.custom.min.js'),
		array('clients.js')	
	),	
	'css' => array(
		array('sunny/jquery-ui-1.7.3.custom.css')
	)
);

$config['groups']['edit_stats'] = array(
	
	'js' => array(
		array('jquery-ui-1.7.3.custom.min.js'),
		array('edit_stats.js')	
	),	
	'css' => array(
		array('sunny/jquery-ui-1.7.3.custom.css')
	)
);

$config['groups']['logbook'] = array(
	
	'js' => array(
	
		array('jquery-ui-1.7.3.custom.min.js'),
		array('jwplayer.js'),
		array('logbook.js')
	
	),	
	'css' => array(
		array('sunny/jquery-ui-1.7.3.custom.css')
	)
);

$config['groups']['calendar'] = array(
	
	'js' => array(
	
		array('calendar.js')
	
	)
);

$config['groups']['workout_generator'] = array(
	
	'js' => array(
	
		array('jquery-ui-1.7.3.custom.min.js'),
		array('jquery.form.js'),
		array('jquery.sticky.js'),
		array('daterangepicker.jQuery.js'),
		array('jquery.blockUI.js'),
		array('workout_generator.js')
	
	),
	
	'css' => array(
		
		array('ui.daterangepicker.css'),
		array('sunny/jquery-ui-1.7.3.custom.css')
	)
);

$config['groups']['admin_skeleton_workout'] = array(
	
	'js' => array(
	
		array('jquery-ui-1.7.3.custom.min.js'),
		array('jquery.form.js'),
		array('jquery.sticky.js'),
		array('admin_skeleton_workout.js')
	
	),
	
	'css' => array(
		
		array('sunny/jquery-ui-1.7.3.custom.css')
	)
);

$config['groups']['register'] = array(
	
	'js' => array(	
		array('jquery.form.js'),
		array('jquery.validate.min.js'),
		array('register.js')	
	)
);


// jQuery UI (latest, as of 1.xx)
$config['groups']['jqueryui'] = array(
	
	'js' => array(
	
		array('http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.js', 'http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js', TRUE, FALSE),
		array('http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.js', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js', TRUE, FALSE)
	
	)
);





// YUI (latest, as of 2.x.x)
$config['groups']['yui'] = array(
	
	'js' => array(
	
		// JS Core
		array('http://ajax.googleapis.com/ajax/libs/yui/2/build/yuiloader/yuiloader.js', 'http://ajax.googleapis.com/ajax/libs/yui/2/build/yuiloader/yuiloader.js', TRUE, FALSE),
		array('http://ajax.googleapis.com/ajax/libs/yui/2/build/dom/dom.js', 'http://ajax.googleapis.com/ajax/libs/yui/2/build/dom/dom-min.js', TRUE, FALSE),
		array('http://ajax.googleapis.com/ajax/libs/yui/2/build/event/event.js', 'http://ajax.googleapis.com/ajax/libs/yui/2/build/event/event-min.js', TRUE, FALSE)
			

	),
	
	
	'css' => array(
	
		// CSS Core
		array('http://ajax.googleapis.com/ajax/libs/yui/2/build/fonts/fonts.css', 'screen', 'http://ajax.googleapis.com/ajax/libs/yui/2/build/fonts/fonts-min.css', TRUE, FALSE),	
		array('http://ajax.googleapis.com/ajax/libs/yui/2/build/reset/reset.css', 'screen', 'http://ajax.googleapis.com/ajax/libs/yui/2/build/reset/reset-min.css', TRUE, FALSE),
		array('http://ajax.googleapis.com/ajax/libs/yui/2/build/grids/grids.css', 'screen', 'http://ajax.googleapis.com/ajax/libs/yui/2/build/grids/grids-min.css', TRUE, FALSE),
		array('http://ajax.googleapis.com/ajax/libs/yui/2/build/base/base.css', 'screen', 'http://ajax.googleapis.com/ajax/libs/yui/2/build/base/base-min.css', TRUE, FALSE)

		//CSS for Controls: Uncomment as Needed
		//,array('http://ajax.googleapis.com/ajax/libs/yui/2/build/autocomplete/assets/skins/sam/autocomplete.css', 'screen', TRUE, FALSE), 
		//array('http://ajax.googleapis.com/ajax/libs/yui/2/build/container/assets/skins/sam/container.css', 'screen', TRUE, FALSE), 
		//array('http://ajax.googleapis.com/ajax/libs/yui/2/build/menu/assets/skins/sam/menu.css', 'screen', TRUE, FALSE), 
		//array('http://ajax.googleapis.com/ajax/libs/yui/2/build/button/assets/skins/sam/button.css', 'screen', TRUE, FALSE), 
		//array('http://ajax.googleapis.com/ajax/libs/yui/2/build/calendar/assets/skins/sam/calendar.css', 'screen', TRUE, FALSE), 
		//array('http://ajax.googleapis.com/ajax/libs/yui/2/build/carousel/assets/skins/sam/carousel.css', 'screen', TRUE, FALSE), 
		//array('http://ajax.googleapis.com/ajax/libs/yui/2/build/slider/assets/skins/sam/slider.css', 'screen', TRUE, FALSE), 
		//array('http://ajax.googleapis.com/ajax/libs/yui/2/build/colorpicker/assets/skins/sam/colorpicker.css', 'screen', TRUE, FALSE), 
		//array('http://ajax.googleapis.com/ajax/libs/yui/2/build/datatable/assets/skins/sam/datatable.css', 'screen', TRUE, FALSE), 
		//array('http://ajax.googleapis.com/ajax/libs/yui/2/build/editor/assets/skins/sam/editor.css', 'screen', TRUE, FALSE), 
		//array('http://ajax.googleapis.com/ajax/libs/yui/2/build/resize/assets/skins/sam/resize.css', 'screen', TRUE, FALSE), 
		//array('http://ajax.googleapis.com/ajax/libs/yui/2/build/imagecropper/assets/skins/sam/imagecropper.css', 'screen', TRUE, FALSE), 
		//array('http://ajax.googleapis.com/ajax/libs/yui/2/build/layout/assets/skins/sam/layout.css', 'screen', TRUE, FALSE),
		//array('http://ajax.googleapis.com/ajax/libs/yui/2/build/tabview/assets/skins/sam/tabview.css', 'screen', TRUE, FALSE), 
		//array('http://ajax.googleapis.com/ajax/libs/yui/2/build/treeview/assets/skins/sam/treeview.css', 'screen', TRUE, FALSE),
		//array('http://ajax.googleapis.com/ajax/libs/yui/2/build/editor/assets/skins/sam/simpleeditor.css', 'screen', TRUE, FALSE)
	
	)

);

$config['groups']['select_trainer'] = array(
	
	'js' => array(
	
		array('jquery-ui-1.7.3.custom.min.js'),
		array('select_trainer.js')
	
	),	
	'css' => array(
		array('sunny/jquery-ui-1.7.3.custom.css')
	)
);