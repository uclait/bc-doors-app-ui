<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */

	Router::connect('/', array('controller' => 'home', 'action' => 'index'));

	//Router::connect('/', array('controller' => 'home', 'action' => 'index'));
    //Router::connect('/', array('controller' => 'general', 'action' => 'headers'));

	//Router::connect('/cfs/photos', array('controller' => 'photos', 'action' => 'xml_status'));

/**
 * ...and connect the rest of 'Pages' controller's URLs.
 */

	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));
        
    Router::connect('/html/:controller/:action/*', array('prefix' => 'html', 'html' => true));
	Router::connect('/xml/:controller/:action/*', array('prefix' => 'xml', 'xml' => true));
	Router::connect('/json/:controller/:action/*', array('prefix' => 'json', 'json' => true));

	Router::connect('/rest/xml/:controller/:action/*', array('prefix' => 'xml', 'rest' => true));

/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';
