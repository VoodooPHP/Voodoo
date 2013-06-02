<?php
/**
 * -----------------------------------------------------------------------------
 * VoodooPHP
 * -----------------------------------------------------------------------------
 * @author      Mardix (http://twitter.com/mardix)
 * @github      https://github.com/mardix/Voodoo
 * @package     VoodooPHP
 *
 * @copyright   (c) 2012 Mardix (http://github.com/mardix)
 * @license     MIT
 * -----------------------------------------------------------------------------
 *
 * @name        Voodooist/setup
 * @desc        A setup file that create your Voodoo environment, include your
 *              App directory, your classes etc.
 *              /PATH/App/Conf/app.json will be created. It a JSON file
 *              that contains your MVC stucture. setup.php will read it and create the files
 *              and directory if not exist.
 *              Also, a /PATH/App/voodooist.php will be created for you to setup your app
 *
 * @run         To execute this file, run the code below in your command line
 *              cd /PATH/Voodoo/Voodooist
 *              php -f ./setup.php
 *
 *              Once your application is created, next time you want to set it up:
 * 
 *              cd /PATH/App
 *              php -f ./voodooist.php
 */

use Voodoo\Voodooist;

require_once dirname(__DIR__)."/autoload.php";

date_default_timezone_set("America/New_York");

// The application root dir where your application's file will be created at
$appRootDir = dirname(dirname(__DIR__));


try {
    Voodooist\Voodooist::create($appRootDir);
} catch (\Exception $e) {
    echo "EXCEPTION: ".$e->getMessage();
}
