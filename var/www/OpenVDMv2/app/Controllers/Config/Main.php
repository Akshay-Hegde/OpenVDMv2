<?php

namespace Controllers\Config;
use Core\Controller;
use Core\View;
use Helpers\Session;
use Helpers\Url;

class Main extends Controller {
    
    private $_warehouseModel;
    private $_tasksModel;
    private $_collectionSystemTransfersModel;
    private $_extraDirectoriesModel;
    private $_cruiseDataTransfersModel;
    
    public function __construct(){
        
        if(!Session::get('loggedin')){
            Url::redirect('config/login');
        }
        
        $this->_warehouseModel = new \Models\Warehouse();
        $this->_tasksModel = new \Models\Config\Tasks();
        $this->_collectionSystemTransfersModel = new \Models\Config\CollectionSystemTransfers();
        $this->_extraDirectoriesModel = new \Models\Config\ExtraDirectories();
        $this->_cruiseDataTransfersModel = new \Models\Config\CruiseDataTransfers();
    }

    public function index(){
            
        $data['title'] = 'Configuration';
        $data['javascript'] = array('main_config');
        $data['cruiseID'] = $this->_warehouseModel->getCruiseID();
        $data['cruiseStartDate'] = $this->_warehouseModel->getCruiseStartDate();
        $data['cruiseEndDate'] = $this->_warehouseModel->getCruiseEndDate();
        $data['systemStatus'] = $this->_warehouseModel->getSystemStatus();
        $data['tasks'] = $this->_tasksModel->getTasks();
        $data['collectionSystemTransfers'] = $this->_collectionSystemTransfersModel->getCollectionSystemTransfers();
        $data['requiredCruiseDataTransfers'] = $this->_cruiseDataTransfersModel->getRequiredCruiseDataTransfers();
        $data['cruiseDataTransfers'] = $this->_cruiseDataTransfersModel->getCruiseDataTransfers();
        
        View::rendertemplate('header',$data);
        View::render('Config/main',$data);
        View::rendertemplate('footer',$data);
    }
    
    public function editCruiseID(){

        $data['title'] = 'Configuration';
        $data['css'] = array('datetimepicker');
        $data['javascript'] = array('datetimepicker', 'cruiseIDFormHelper');
        $data['cruiseID'] = $this->_warehouseModel->getCruiseID();
        $data['cruiseStartDate'] = $this->_warehouseModel->getCruiseStartDate();
        $data['cruiseEndDate'] = $this->_warehouseModel->getCruiseEndDate();
        $data['cruises'] = $this->_warehouseModel->getCruises();
        
        if(isset($_POST['submit'])) {

            $cruiseID = null;
            $setLatestLowering = false;

            if ($_POST['cruiseID']) {
                $cruiseID = $_POST['cruiseID'];
            } else {
                $cruiseID = $this->_warehouseModel->getCruiseID();
            }
            $cruiseStartDate = $_POST['cruiseStartDate'];
            $cruiseEndDate = $_POST['cruiseEndDate'];

            if (strcmp($cruiseID, $this->_warehouseModel->getCruiseID()) != 0) {
                $setLatestLowering = true;
                $cruiseDates = $this->_warehouseModel->getCruiseDates($_POST['cruiseID']);
                $cruiseStartDate = $cruiseDates['cruiseStartDate'];
                $cruiseEndDate = $cruiseDates['cruiseEndDate'];
            }

            if($cruiseID == ''){
                $error[] = 'Cruise ID is required';
            } elseif(!preg_match('/([0-9]{4})\/([0-9]{2})\/([0-9]{2}) ([0-9]{2}):([0-9]{2})/', $cruiseStartDate)){
                $error[] = 'Valid Cruise Start Date is required';              
            } elseif(strcmp($cruiseEndDate,'') != 0 && !preg_match('/([0-9]{4})\/([0-9]{2})\/([0-9]{2}) ([0-9]{2}):([0-9]{2})/', $cruiseEndDate)){
                $error[] = 'Improperly formatted Cruise End Date';
            } else {
                $warehouseData = $this->_warehouseModel->getShipboardDataWarehouseConfig();
                
                if (!is_dir($warehouseData['shipboardDataWarehouseBaseDir'] . '/' . $cruiseID)) {
                    $error[] = 'A Cruise Data Directory for that Cruise ID does not exist';
                }
            }
            
            if(!$error){

                $this->_warehouseModel->setCruiseID(array('value' => $cruiseID));
                $this->_warehouseModel->setCruiseStartDate(array('value' => $cruiseStartDate));
                $this->_warehouseModel->setCruiseEndDate(array('value' => $cruiseEndDate));

                //$_warehouseModel = new \models\warehouse();
                $gmData['cruiseID'] = $this->_warehouseModel->getCruiseID();
        
                # create the gearman client
                $gmc= new \GearmanClient();

                # add the default server (localhost)
                $gmc->addServer();

                #submit job to Gearman
                #$job_handle = $gmc->doBackground("updateCruiseDirectory", json_encode($gmData));
                $data['jobResults'] = json_decode($gmc->doNormal("setCruiseDataDirectoryPermissions", json_encode($gmData)));
                $data['jobResults'] = json_decode($gmc->doNormal("exportOVDMConfig", json_encode($gmData)));

                //var_dump($this->_warehouseModel->getLowerings());
                //var_dump($this->_warehouseModel->getLatestLowering());
        
                sleep(1);
                Session::set('message','Cruise ID Updated');
                Url::redirect('config');
            } else {
                $data['cruiseID'] = $cruiseID;
                $data['cruiseStartDate'] = $cruiseStartDate;
            }
        }
        
        View::rendertemplate('header',$data);
        View::render('Config/editCruiseID',$data, $error);
        View::rendertemplate('footer',$data);
    }
    
    public function enableSystem() {

        $this->_warehouseModel->enableSystem();
        Url::redirect($_SERVER['HTTP_REFERER'], true);
        //Url::redirect('config');
    }
    
    public function disableSystem() {

        $this->_warehouseModel->disableSystem();
        Url::redirect($_SERVER['HTTP_REFERER'], true);
        //Url::redirect('config');
    }
    
    public function rebuildCruiseDirectory() {

        //$_warehouseModel = new \models\warehouse();
        $gmData['cruiseID'] = $this->_warehouseModel->getCruiseID();
        
        # create the gearman client
        $gmc= new \GearmanClient();

        # add the default server (localhost)
        $gmc->addServer();

        #submit job to Gearman
        #$job_handle = $gmc->doBackground("updateCruiseDirectory", json_encode($gmData));
        $data['jobResults'] = json_decode($gmc->doNormal("rebuildCruiseDirectory", json_encode($gmData)));
    
        #additional data needed for view
        $data['title'] = 'Configuration';
        $data['javascript'] = array('main_config');
        $data['cruiseID'] = $this->_warehouseModel->getCruiseID();
        $data['cruiseStartDate'] = $this->_warehouseModel->getCruiseStartDate();
        $data['cruiseEndDate'] = $this->_warehouseModel->getCruiseEndDate();
        $data['systemStatus'] = $this->_warehouseModel->getSystemStatus();
        $data['tasks'] = $this->_tasksModel->getTasks();
        $data['collectionSystemTransfers'] = $this->_collectionSystemTransfersModel->getCollectionSystemTransfers();
        $data['requiredCruiseDataTransfers'] = $this->_cruiseDataTransfersModel->getRequiredCruiseDataTransfers();
        $data['cruiseDataTransfers'] = $this->_cruiseDataTransfersModel->getCruiseDataTransfers();

        $data['jobName'] = 'Rebuild Cruise Directory';

        View::rendertemplate('header',$data);
        View::render('Config/main',$data);
        View::rendertemplate('footer',$data);
    }
    
    public function rebuildMD5Summary() {

        //$_warehouseModel = new \Models\Warehouse();
        $gmData['cruiseID'] = $this->_warehouseModel->getCruiseID();
        
        # create the gearman client
        $gmc= new \GearmanClient();

        # add the default server (localhost)
        $gmc->addServer();

        #submit job to Gearman
        $job_handle = $gmc->doBackground("rebuildMD5Summary", json_encode($gmData));
    
        sleep(1);
        
        Url::redirect('config');
    }
    
    public function rebuildDataDashboard() {

        //$_warehouseModel = new \Models\Warehouse();
        $gmData['cruiseID'] = $this->_warehouseModel->getCruiseID();
        
        # create the gearman client
        $gmc= new \GearmanClient();

        # add the default server (localhost)
        $gmc->addServer();

        #submit job to Gearman
        $job_handle = $gmc->doBackground("rebuildDataDashboard", json_encode($gmData));
    
        sleep(1);

        Url::redirect('config');
    }
    
    public function setupNewCruise() {

        //$collectionSystemTransfersModel;
        //$collectionSystemTransfersModel = new \Models\Config\CollectionSystemTransfers();
        $SSDW = null;
            
        $requiredCruiseDataTransfers = $this->_cruiseDataTransfersModel->getRequiredCruiseDataTransfers();
        foreach($requiredCruiseDataTransfers as $requiredCruiseDataTransfer) {
            if(strcmp($requiredCruiseDataTransfer->name, 'SSDW') === 0) {
                $SSDW = $requiredCruiseDataTransfer;
                $data['shipToShoreTransfersEnable'] = $SSDW->enable;
                break;
            }
        }

        $data['collectionSystemTransfers'] = $this->_collectionSystemTransfersModel->getCollectionSystemTransfers();
        
        $data['title'] = 'Configuration';
        $data['css'] = array('datetimepicker');
        $data['javascript'] = array('datetimepicker');
        $data['cruiseID'] = '';
        $data['cruiseStartDate'] = '';
        $data['cruiseEndDate'] = '';
//        $error = array();

        if(isset($_POST['submit'])){
            $cruiseID = $_POST['cruiseID'];
            $cruiseStartDate = $_POST['cruiseStartDate'];
            $cruiseEndDate = $_POST['cruiseEndDate'];

            if($cruiseID == ''){
                $error[] = 'Cruise ID is required';
            } elseif(!preg_match('/([0-9]{4})\/([0-9]{2})\/([0-9]{2}) ([0-9]{2}):([0-9]{2})/', $cruiseStartDate)){
                $error[] = 'Valid Cruise Start Date is required';
            } elseif($cruiseEndDate != '' && !preg_match('/([0-9]{4})\/([0-9]{2})\/([0-9]{2}) ([0-9]{2}):([0-9]{2})/', $cruiseEndDate)){
                $error[] = 'Improperly formatted Cruise End Date';
            } else {
                $warehouseData = $this->_warehouseModel->getShipboardDataWarehouseConfig();  
                if (is_dir($warehouseData['shipboardDataWarehouseBaseDir'] . '/' . $cruiseID)) {
                    $error[] = 'A Cruise Data Directory for that Cruise ID already exists';
                }
            }
                
            if(!$error){
                
                $this->_warehouseModel->setCruiseID(array('value' => $cruiseID));
                $this->_warehouseModel->setCruiseStartDate(array('value' => $cruiseStartDate));
                $this->_warehouseModel->setCruiseEndDate(array('value' => $cruiseEndDate));
                $gmData['cruiseID'] = $this->_warehouseModel->getCruiseID();
        
                # create the gearman client
                $gmc= new \GearmanClient();

                # add the default server (localhost)
                $gmc->addServer();

                #submit job to Gearman
                #$job_handle = $gmc->doBackground("rebuildCruiseDirectory", json_encode($gmData));
                $data['jobResults'] = json_decode($gmc->doNormal("setupNewCruise", json_encode($gmData)));
    
        
                #additional data needed for view
                $data['title'] = 'Configuration';
                $data['javascript'] = array('main_config');
                $data['cruiseID'] = $this->_warehouseModel->getCruiseID();                                                           
                $data['systemStatus'] = $this->_warehouseModel->getSystemStatus();
                $data['tasks'] = $this->_tasksModel->getTasks();
                $data['collectionSystemTransfers'] = $this->_collectionSystemTransfersModel->getCollectionSystemTransfers();
                $data['requiredCruiseDataTransfers'] = $this->_cruiseDataTransfersModel->getRequiredCruiseDataTransfers();
                $data['cruiseDataTransfers'] = $this->_cruiseDataTransfersModel->getCruiseDataTransfers();
                
                $data['jobName'] = 'Setup New Cruise';

                View::rendertemplate('header',$data);
                View::render('Config/main',$data);
                View::rendertemplate('footer',$data);

                return true;

            } else {
                $data['cruiseID'] = $cruiseID;
                $data['cruiseStartDate'] = $cruiseStartDate;
                $data['cruiseEndDate'] = $cruiseEndDate;
            }
        } elseif(isset($_POST)) {
            $data['cruiseID'] = $_POST['cruiseID'];
            $data['cruiseStartDate'] = $_POST['cruiseStartDate'];
            $data['cruiseEndDate'] = $_POST['cruiseEndDate'];
            if(isset($_POST['disableSSDW'])) {
                $this->_cruiseDataTransfersModel->disableCruiseDataTransfer($SSDW->cruiseDataTransferID);
            }
            
            if(isset($_POST['enableSSDW'])) {
                $this->_cruiseDataTransfersModel->enableCruiseDataTransfer($SSDW->cruiseDataTransferID);
            }
            
            foreach($data['collectionSystemTransfers'] as $row) {
                if(isset($_POST['enableCS' . $row->collectionSystemTransferID])) {
                    $this->_collectionSystemTransfersModel->enableCollectionSystemTransfer($row->collectionSystemTransferID);
                    break;
                }
                if(isset($_POST['disableCS' . $row->collectionSystemTransferID])) {
                    $this->_collectionSystemTransfersModel->disableCollectionSystemTransfer($row->collectionSystemTransferID);
                    break;
                }
            }
            
            $data['collectionSystemTransfers'] = $this->_collectionSystemTransfersModel->getCollectionSystemTransfers();
            $data['shipToShoreTransfersEnable'] = $this->_cruiseDataTransfersModel->getCruiseDataTransfer($SSDW->cruiseDataTransferID)[0]->enable;

        }
        
        View::rendertemplate('header',$data);
        View::render('Config/newCruiseID',$data, $error);
        View::rendertemplate('footer',$data);
    }
    
    public function finalizeCurrentCruise() {

        $gmData = array();
        
        # create the gearman client
        $gmc= new \GearmanClient();

        # add the default server (localhost)
        $gmc->addServer();

        #submit job to Gearman
        #$job_handle = $gmc->doBackground("rebuildCruiseDirectory", json_encode($gmData));
        $data['jobResults'] = json_decode($gmc->doBackground("finalizeCurrentCruise", json_encode($gmData)));
        
        sleep(1);

        Url::redirect('config');
    
    }
    
    public function exportOVDMConfig() {
        
        $gmData = array();
        
        # create the gearman client
        $gmc= new \GearmanClient();

        # add the default server (localhost)
        $gmc->addServer();

        #submit job to Gearman
        #$job_handle = $gmc->doBackground("rebuildCruiseDirectory", json_encode($gmData));
        $data['jobResults'] = json_decode($gmc->doBackground("exportOVDMConfig", json_encode($gmData)));
        
        sleep(1);

        Url::redirect('config');
    
    }
    
    public function rsyncPublicDataToCruiseData() {
        
        $gmData = array();
        
        # create the gearman client
        $gmc= new \GearmanClient();

        # add the default server (localhost)
        $gmc->addServer();

        #submit job to Gearman
        #$job_handle = $gmc->doBackground("rebuildCruiseDirectory", json_encode($gmData));
        $data['jobResults'] = json_decode($gmc->doBackground("rsyncPublicDataToCruiseData", json_encode($gmData)));
        
        sleep(1);

        Url::redirect('config');
    
    }
}
