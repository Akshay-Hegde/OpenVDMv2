<?php
if (file_exists('vendor/autoload.php')) {
    require 'vendor/autoload.php';
} else {
    echo "<h1>Please install via composer.json</h1>";
    echo "<p>Install Composer instructions: <a href='https://getcomposer.org/doc/00-intro.md#globally'>https://getcomposer.org/doc/00-intro.md#globally</a></p>";
    echo "<p>Once composer is installed navigate to the working directory in your terminal/command promt and enter 'composer install'</p>";
    exit;
}

if (!is_readable('app/Core/Config.php')) {
    die('No Config.php found, configure and rename Config.example.php to Config.php in app/Core.');
}

/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 *
 */
    define('ENVIRONMENT', 'development');
/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but production will hide them.
 */

if (defined('ENVIRONMENT')) {
    switch (ENVIRONMENT) {
        case 'development':
            error_reporting(E_ALL);
            break;
        case 'production':
            error_reporting(0);
            break;
        default:
            exit('The application environment is not set correctly.');
    }

}

//initiate config
new Core\Config();

//create alias for Router
use Core\Router;
use Helpers\Hooks;

//define routes
// Configuration-related pages
Router::any('config', '\Controllers\Config\Main@index');
Router::any('config/enableSystem', '\Controllers\Config\Main@enableSystem');
Router::any('config/disableSystem', '\Controllers\Config\Main@disableSystem');
Router::any('config/setupNewCruise', '\Controllers\Config\Main@setupNewCruise');
Router::any('config/finalizeCurrentCruise', '\Controllers\Config\Main@finalizeCurrentCruise');
Router::any('config/exportOVDMConfig', '\Controllers\Config\Main@exportOVDMConfig');
Router::any('config/rebuildMD5Summary', '\Controllers\Config\Main@rebuildMD5Summary');
Router::any('config/rebuildTransferLogSummary', '\Controllers\Config\Main@rebuildTransferLogSummary');
Router::any('config/rebuildCruiseDirectory', '\Controllers\Config\Main@rebuildCruiseDirectory');
Router::any('config/rebuildDataDashboard', '\Controllers\Config\Main@rebuildDataDashboard');
Router::any('config/editCruiseID', '\Controllers\Config\Main@editCruiseID');
Router::any('config/editShipboardDataWarehouse', '\Controllers\Config\Main@editShipboardDataWarehouse');
Router::any('config/login', '\Controllers\Config\Auth@login');
Router::any('config/logout', '\Controllers\Config\Auth@logout');

Router::any('config/system', '\Controllers\Config\System@index');
Router::any('config/system/editShipboardDataWarehouse', '\Controllers\Config\System@editShipboardDataWarehouse');
Router::any('config/system/testShipboardDataWarehouse', '\Controllers\Config\System@testShipboardDataWarehouse');
Router::any('config/system/editShoresideDataWarehouse', '\Controllers\Config\System@editShoresideDataWarehouse');
Router::any('config/system/testShoresideDataWarehouse', '\Controllers\Config\System@testShoresideDataWarehouse');
Router::any('config/system/editExtraDirectories/(:num)', '\Controllers\Config\System@editExtraDirectories');
Router::any('config/system/editShipToShoreTransfers/(:num)', '\Controllers\Config\System@editShipToShoreTransfers');
Router::any('config/system/enableShipToShoreTransfers/(:num)', '\Controllers\Config\System@enableShipToShoreTransfers');
Router::any('config/system/disableShipToShoreTransfers/(:num)', '\Controllers\Config\System@disableShipToShoreTransfers');
Router::any('config/system/enableShipToShoreBWLimit', '\Controllers\Config\System@enableShipToShoreBWLimit');
Router::any('config/system/disableShipToShoreBWLimit', '\Controllers\Config\System@disableShipToShoreBWLimit');
Router::any('config/system/editShipToShoreBWLimit', '\Controllers\Config\System@editShipToShoreBWLimit');
Router::any('config/system/enableMD5FilesizeLimit', '\Controllers\Config\System@enableMD5FilesizeLimit');
Router::any('config/system/disableMD5FilesizeLimit', '\Controllers\Config\System@disableMD5FilesizeLimit');
Router::any('config/system/editMD5FilesizeLimit', '\Controllers\Config\System@editMD5FilesizeLimit');

Router::any('config/users/edit/(:num)', '\Controllers\Config\Users@edit');
#Router::any('config/system/delete/(:num)', '\Controllers\Config\System@delete');

Router::any('config/collectionSystemTransfers', '\Controllers\Config\CollectionSystemTransfers@index');
Router::any('config/collectionSystemTransfers/add', '\Controllers\Config\CollectionSystemTransfers@add');
Router::any('config/collectionSystemTransfers/edit/(:num)', '\Controllers\Config\CollectionSystemTransfers@edit');
Router::any('config/collectionSystemTransfers/delete/(:num)', '\Controllers\Config\CollectionSystemTransfers@delete');
Router::any('config/collectionSystemTransfers/enable/(:num)', '\Controllers\Config\CollectionSystemTransfers@enable');
Router::any('config/collectionSystemTransfers/disable/(:num)', '\Controllers\Config\CollectionSystemTransfers@disable');
Router::any('config/collectionSystemTransfers/test/(:num)', '\Controllers\Config\CollectionSystemTransfers@test');
Router::any('config/collectionSystemTransfers/run/(:num)', '\Controllers\Config\CollectionSystemTransfers@run');
Router::any('config/collectionSystemTransfers/stop/(:num)', '\Controllers\Config\CollectionSystemTransfers@stop');

Router::any('config/cruiseDataTransfers', '\Controllers\Config\CruiseDataTransfers@index');
Router::any('config/cruiseDataTransfers/add', '\Controllers\Config\CruiseDataTransfers@add');
Router::any('config/cruiseDataTransfers/edit/(:num)', '\Controllers\Config\CruiseDataTransfers@edit');
Router::any('config/cruiseDataTransfers/delete/(:num)', '\Controllers\Config\CruiseDataTransfers@delete');
Router::any('config/cruiseDataTransfers/enable/(:num)', '\Controllers\Config\CruiseDataTransfers@enable');
Router::any('config/cruiseDataTransfers/disable/(:num)', '\Controllers\Config\CruiseDataTransfers@disable');
Router::any('config/cruiseDataTransfers/test/(:num)', '\Controllers\Config\CruiseDataTransfers@test');
Router::any('config/cruiseDataTransfers/run/(:num)', '\Controllers\Config\CruiseDataTransfers@run');
Router::any('config/cruiseDataTransfers/stop/(:num)', '\Controllers\Config\CruiseDataTransfers@stop');

Router::any('config/extraDirectories', '\Controllers\Config\ExtraDirectories@index');
Router::any('config/extraDirectories/add', '\Controllers\Config\ExtraDirectories@add');
Router::any('config/extraDirectories/edit/(:num)', '\Controllers\Config\ExtraDirectories@edit');
Router::any('config/extraDirectories/delete/(:num)', '\Controllers\Config\ExtraDirectories@delete');
Router::any('config/extraDirectories/enable/(:num)', '\Controllers\Config\ExtraDirectories@enable');
Router::any('config/extraDirectories/disable/(:num)', '\Controllers\Config\ExtraDirectories@disable');

Router::any('config/shipToShoreTransfers', '\Controllers\Config\ShipToShoreTransfers@index');
Router::any('config/shipToShoreTransfers/add', '\Controllers\Config\ShipToShoreTransfers@add');
Router::any('config/shipToShoreTransfers/edit/(:num)', '\Controllers\Config\ShipToShoreTransfers@edit');
Router::any('config/shipToShoreTransfers/delete/(:num)', '\Controllers\Config\ShipToShoreTransfers@delete');
Router::any('config/shipToShoreTransfers/enable/(:num)', '\Controllers\Config\ShipToShoreTransfers@enable');
Router::any('config/shipToShoreTransfers/disable/(:num)', '\Controllers\Config\ShipToShoreTransfers@disable');
Router::any('config/shipToShoreTransfers/run', '\Controllers\Config\ShipToShoreTransfers@run');
Router::any('config/shipToShoreTransfers/stop', '\Controllers\Config\ShipToShoreTransfers@stop');
Router::any('config/shipToShoreTransfers/enableShipToShoreTransfers', '\Controllers\Config\ShipToShoreTransfers@enableShipToShoreTransfers');
Router::any('config/shipToShoreTransfers/disableShipToShoreTransfers', '\Controllers\Config\ShipToShoreTransfers@disableShipToShoreTransfers');

Router::any('config/messages', '\Controllers\Config\Messages@index');
Router::any('config/messages/delete/(:num)', '\Controllers\Config\Messages@delete');
Router::any('config/messages/viewedMessage/(:num)', '\Controllers\Config\Messages@viewedMessage');
Router::any('config/messages/viewAllMessages', 'Controllers\Config\Messages@viewAllMessages');
Router::any('config/messages/deleteMessage/(:num)', '\Controllers\Config\Messages@deleteMessage');
Router::any('config/messages/deleteAllMessages', 'Controllers\Config\Messages@deleteAllMessages');

//main site related
Router::any('dataDashboard', '\Controllers\DataDashboard\DataDashboard@index');
Router::any('dataDashboard/position', '\Controllers\DataDashboard\DataDashboard@position');
Router::any('dataDashboard/weather', '\Controllers\DataDashboard\DataDashboard@weather');
Router::any('dataDashboard/soundVelocity', '\Controllers\DataDashboard\DataDashboard@soundVelocity');
Router::any('dataDashboard/qualityControl', '\Controllers\DataDashboard\DataDashboard@qualityControl');
Router::any('dataDashboard/qualityControlShowDataFileStats/(:num)', '\Controllers\DataDashboard\DataDashboard@qualityControlShowDataFileStats');
Router::any('dataDashboard/qualityControlShowDataTypeStats/(:any)', '\Controllers\DataDashboard\DataDashboard@qualityControlShowDataTypeStats');

//define API-related routes
Router::any('api/warehouse/getCruiseConfig', 'Controllers\Api\Warehouse@getCruiseConfig');
Router::any('api/warehouse/getCruiseID', 'Controllers\Api\Warehouse@getCruiseID');
Router::any('api/warehouse/getCruiseSize', 'Controllers\Api\Warehouse@getCruiseSize');
Router::any('api/warehouse/getCruiseStartDate', 'Controllers\Api\Warehouse@getCruiseStartDate');
Router::any('api/warehouse/getFreeSpace', 'Controllers\Api\Warehouse@getFreeSpace');
Router::any('api/warehouse/getMD5FilesizeLimit', 'Controllers\Api\Warehouse@getMD5FilesizeLimit');
Router::any('api/warehouse/getMD5FilesizeLimitStatus', 'Controllers\Api\Warehouse@getMD5FilesizeLimitStatus');
Router::any('api/warehouse/getShipboardDataWarehouseConfig', 'Controllers\Api\Warehouse@getShipboardDataWarehouseConfig');
Router::any('api/warehouse/getShipboardDataWarehouseStatus', 'Controllers\Api\Warehouse@getShipboardDataWarehouseStatus');
Router::any('api/warehouse/getShipToShoreBWLimit', 'Controllers\Api\Warehouse@getShipToShoreBWLimit');
Router::any('api/warehouse/getShipToShoreBWLimitStatus', 'Controllers\Api\Warehouse@getShipToShoreBWLimitStatus');
Router::any('api/warehouse/getSystemStatus', 'Controllers\Api\Warehouse@getSystemStatus');
Router::any('api/warehouse/getTransferLogSummary', 'Controllers\Api\Warehouse@getTransferLogSummary');

Router::any('api/collectionSystemTransfers/getCollectionSystemTransfers', 'Controllers\Api\CollectionSystemTransfers@getCollectionSystemTransfers');
Router::any('api/collectionSystemTransfers/getCollectionSystemTransfer/(:num)', 'Controllers\Api\CollectionSystemTransfers@getCollectionSystemTransfer');
Router::any('api/collectionSystemTransfers/getCollectionSystemTransfersStatuses', 'Controllers\Api\CollectionSystemTransfers@getCollectionSystemTransfersStatuses');
Router::any('api/collectionSystemTransfers/setErrorCollectionSystemTransfer/(:num)', 'Controllers\Api\CollectionSystemTransfers@setErrorCollectionSystemTransfer');
Router::any('api/collectionSystemTransfers/setRunningCollectionSystemTransfer/(:num)', 'Controllers\Api\CollectionSystemTransfers@setRunningCollectionSystemTransfer');
Router::any('api/collectionSystemTransfers/setIdleCollectionSystemTransfer/(:num)', 'Controllers\Api\CollectionSystemTransfers@setIdleCollectionSystemTransfer');

Router::any('api/cruiseDataTransfers/getCruiseDataTransfers', 'Controllers\Api\CruiseDataTransfers@getCruiseDataTransfers');
Router::any('api/cruiseDataTransfers/getCruiseDataTransfer/(:num)', 'Controllers\Api\CruiseDataTransfers@getCruiseDataTransfer');
Router::any('api/cruiseDataTransfers/getRequiredCruiseDataTransfers', 'Controllers\Api\CruiseDataTransfers@getRequiredCruiseDataTransfers');
Router::any('api/cruiseDataTransfers/getCruiseDataTransfersStatuses', 'Controllers\Api\CruiseDataTransfers@getCruiseDataTransfersStatuses');
Router::any('api/cruiseDataTransfers/getRequiredCruiseDataTransfersStatuses', 'Controllers\Api\CruiseDataTransfers@getRequiredCruiseDataTransfersStatuses');
Router::any('api/cruiseDataTransfers/setErrorCruiseDataTransfer/(:num)', 'Controllers\Api\CruiseDataTransfers@setErrorCruiseDataTransfer');
Router::any('api/cruiseDataTransfers/setRunningCruiseDataTransfer/(:num)', 'Controllers\Api\CruiseDataTransfers@setRunningCruiseDataTransfer');
Router::any('api/cruiseDataTransfers/setIdleCruiseDataTransfer/(:num)', 'Controllers\Api\CruiseDataTransfers@setIdleCruiseDataTransfer');

Router::any('api/dataDashboard/getCruises', 'Controllers\Api\DataDashboard@getCruises');
Router::any('api/dataDashboard/getDataObjectTypes/(:any)', 'Controllers\Api\DataDashboard@getDataObjectTypes');
Router::any('api/dataDashboard/getDataObjects/(:any)', 'Controllers\Api\DataDashboard@getDataObjects');
Router::any('api/dataDashboard/getDataObjectsByType/(:any)/(:any)', 'Controllers\Api\DataDashboard@getDataObjectsByType');
Router::any('api/dataDashboard/getDataObject/(:num)', 'Controllers\Api\DataDashboard@getDataObject');
Router::any('api/dataDashboard/getDataObjectFile/(:num)', 'Controllers\Api\DataDashboard@getDataObjectFile');
Router::any('api/dataDashboard/getDataObjectFileVisualizer/(:num)', 'Controllers\Api\DataDashboard@getDataObjectFileVisualizerData');
Router::any('api/dataDashboard/getDataObjectFileQualityTests/(:num)', 'Controllers\Api\DataDashboard@getDataObjectFileQualityTests');
Router::any('api/dataDashboard/getDataObjectFileStats/(:num)', 'Controllers\Api\DataDashboard@getDataObjectFileStats');
Router::any('api/dataDashboard/updateDataDashboardObjectsFromManifest/(:any)', 'Controllers\Api\DataDashboard@updateDataDashboardObjectsFromManifest');
Router::any('api/dataDashboard/getRecentData', 'Controllers\Api\DataDashboard@getRecentData');
Router::any('api/dataDashboard/getRecentDataByType/(:any)', 'Controllers\Api\DataDashboard@getRecentDataByType');
Router::any('api/dataDashboard/getRecentDataObjectFileByType/(:any)/(:any)', 'Controllers\Api\DataDashboard@getRecentDataObjectFileByType');
Router::any('api/dataDashboard/updateRecentData', 'Controllers\Api\DataDashboard@updateRecentData');

Router::any('api/extraDirectories/getExtraDirectories', 'Controllers\Api\ExtraDirectories@getExtraDirectories');
Router::any('api/extraDirectories/getExtraDirectory/(:num)', 'Controllers\Api\ExtraDirectories@getExtraDirectory');
Router::any('api/extraDirectories/getRequiredExtraDirectories', 'Controllers\Api\ExtraDirectories@getRequiredExtraDirectories');

Router::any('api/tasks/getTasks', 'Controllers\Api\Tasks@getTasks');
Router::any('api/tasks/getTask/(:num)', 'Controllers\Api\Tasks@getTask');
Router::any('api/tasks/getTaskStatuses', 'Controllers\Api\Tasks@getTaskStatuses');
Router::any('api/tasks/setErrorTask/(:num)', 'Controllers\Api\Tasks@setErrorTask');
Router::any('api/tasks/setRunningTask/(:num)', 'Controllers\Api\Tasks@setRunningTask');
Router::any('api/tasks/setIdleTask/(:num)', 'Controllers\Api\Tasks@setIdleTask');

Router::any('api/shipToShoreTransfers/getShipToShoreTransfers', 'Controllers\Api\ShipToShoreTransfers@getShipToShoreTransfers');
Router::any('api/shipToShoreTransfers/getRequiredShipToShoreTransfers', 'Controllers\Api\ShipToShoreTransfers@getRequiredShipToShoreTransfers');

Router::any('api/messages/viewedMessage/(:num)', 'Controllers\Api\Messages@viewedMessage');
Router::any('api/messages/getRecentMessages', 'Controllers\Api\Messages@getRecentMessages');
Router::any('api/messages/getNewMessagesTotal', 'Controllers\Api\Messages@getNewMessagesTotal');
Router::any('api/messages/newMessage',    'Controllers\Api\Messages@newMessage');

Router::any('api/gearman/newJob/(:any)', 'Controllers\Api\Gearman@newJob');
Router::any('api/gearman/getJobs', 'Controllers\Api\Gearman@getJobs');
Router::any('api/gearman/getJob/(:num)', 'Controllers\Api\Gearman@getJob');
Router::any('api/gearman/clearAllJobsFromDB', 'Controllers\Api\Gearman@clearAllJobsFromDB');


Router::any('', 'Controllers\Welcome@index');
//Router::any('subpage', 'Controllers\Welcome@subPage');

//module routes
$hooks = Hooks::get();
$hooks->run('routes');

//if no route found
Router::error('Core\Error@index');

//turn on old style routing
Router::$fallback = false;

//execute matched routes
Router::dispatch();