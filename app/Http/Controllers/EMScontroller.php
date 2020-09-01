<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\WebServices\EmsService;
use Illuminate\Support\Facades\Storage;
//use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\EMSuserUpload;
use App\Models\Emslogs;
use App\WebServices\OrgSyncService;
use App\Models\emsRoom;


class EMScontroller extends Controller
{
    //
    private  $service ;
    public function __construct()
    {
        $this->service = new EmsService();
    }
    public function emsBookingView(){
        return view('emsBooking');
    }

    public function hello()
    {
        echo "hello";
        $key = config('app.key');
        echo $key;
        //$service = new EmsService();
        var_dump($this->service->getGroups('ss9545@nyu.edu'));
       // $service->addWebUser('abc@nyu.edu', 'abc', 'abc111', 60, )
        die();
    }
    public function addBooking($resevationID,$roomID,$bookingDate,$startTime,$endTime,$statusID,$eventName,$eventTypeID)
    {
        //Testing Data
//        $newBookingID = $this->service->addBooking(44471,101,"2020-09-30T00:00:00","2020-09-30T10:13:00",
//            "2010-09-30T11:13:00",1,"Created from Admin Client",0);
//        $resevationID = 44471;
//        $roomID = 188;
//        $bookingDate = "2020-09-30T00:00:00";
//        $startTime = "2020-09-30T10:13:00";
//        $endTime = "2010-09-30T11:13:00";
//        $statusID = 1;
//        $eventName = "Created from Admin Client";
//        $eventTypeID = 0;
        $result = $this->service->addBooking($resevationID,$roomID,$bookingDate,$startTime,$endTime,$statusID,$eventName,$eventTypeID);
        if(!empty($result['newBookingID'])){
            $emslog = new Emslogs();
            $emslog->userName = $resevationID;
            $emslog->NetID = $bookingDate;
            $emslog->userType = $startTime;
            $emslog->eventRequester = $endTime;
            $emslog->webAppUser = $result['newBookingID'];
            $emslog->uploadedBy = $roomID;
            $emslog->save();
        }
        else{
            //echo "error on".$resevationID." ".$bookingDate." ".$startTime." ".$endTime." ".$eventName;
            $emslog = new Emslogs();
            $emslog->userName = $resevationID;
            $emslog->NetID = $bookingDate;
            $emslog->userType = $startTime;
            $emslog->eventRequester = $endTime;
            $emslog->webAppUser = $result['message'];
            $emslog->uploadedBy = $roomID;
            $emslog->save();
        }

    }
    public function addReservation($groupID,$roomID,$bookingDate,$startTime,$endTime,$statusID,$eventName)
    {
        //Testing Data
//        $newReservation = $this->service->addReservation(68534,301,"2020-08-04T00:00:00","2020-08-04T11:20:00",
//            "2020-08-04T11:30:00",1,"Created from Sam Client");
//
        //调用API
        $result = $this->service->addReservation($groupID,$roomID,$bookingDate,$startTime,$endTime,$statusID,$eventName);
        //将API调用结果存入数据库作为日志
        if(!empty($result['newReservationID'])){
            $emslog = new Emslogs();
            $emslog->userName = $eventName;
            $emslog->NetID = $bookingDate;
            $emslog->userType = $startTime;
            $emslog->eventRequester = $endTime;
            $emslog->webAppUser = $result['newReservationID'];
            $emslog->uploadedBy = $roomID;
            $emslog->save();
        }
        else{
            //echo "error on".$resevationID." ".$bookingDate." ".$startTime." ".$endTime." ".$eventName;
            $emslog = new Emslogs();
            $emslog->userName = $eventName;
            $emslog->NetID = $bookingDate;
            $emslog->userType = $startTime;
            $emslog->eventRequester = $endTime;
            $emslog->webAppUser = $result['message'];
            $emslog->uploadedBy = $roomID;
            $emslog->save();
        }

    }
    public function addReservations(Request $request)
    {
        echo "hello";
        //Save uploaded file to $path
        $path = $request->file('emsReservationUpload')->store('/public/emsUpload');
        //Load the just uploaded excel file
        $path1 = storage_path( 'app/' . $path);
        Excel::load($path1, function($reader) {
            $excelDatas = $reader->formatDates(false)->toArray();
            foreach ($excelDatas as $excelData) {
                if (!empty($excelData)) {
                    //dd($excelData);die();
                    //All required data exist, run create Single User

                    $this->addReservation((string)$excelData['groupid'], (string)$excelData['roomid'], $excelData['bookingdate'], $excelData['starttime'],
                        $excelData['endtime'], (string)$excelData['statusid'], $excelData['eventname']);
                }
            }
        });
        $results['Bulk Import'] = $path . ' Import Complete!';
        return view('emsBooking',['inputs'=>$results]);

    }
    public function addBookings(Request $request)
    {

        //Save uploaded file to $path
        $path = $request->file('emsBookingUpload')->store('/public/emsUpload');
        //Load the just uploaded excel file
        $path1 = storage_path( 'app/' . $path);
        Excel::load($path1, function($reader) {
            $excelDatas = $reader->formatDates(false)->toArray();
            foreach ($excelDatas as $excelData) {
                if (!empty($excelData)) {
                    //dd($excelData);die();
                    //All required data exist, run create Single User

                    $this->addBooking((string)$excelData['reservationid'], (string)$excelData['roomid'], $excelData['bookingdate'], $excelData['starttime'],
                        $excelData['endtime'], (string)$excelData['statusid'], $excelData['eventname'], (string)$excelData['eventtypeid']);
                }
            }
        });
        $results['Bulk Import'] = $path . ' Import Complete!';
        return view('emsBooking',['inputs'=>$results]);

    }
    public function getRooms()
    {
        $rooms = $this->service->getRoomsByBuilding(11);
        foreach ($rooms as $room){
            $emsroom = new emsRoom();
            $emsroom->room = $room['room'];
            $emsroom->description = $room['description'];
            $emsroom->room_id = $room['room_id'];
            $emsroom->building = $room["building"];
            $emsroom->building_id = $room["building_id"];
            $emsroom->save();
        }

    }

    public function buildings()
    {
        $service = new EmsService();
        $buildings = collect($service->getBuildings());
        dd($buildings);die();
        foreach ($buildings as $building) {
            echo $building;
        }
    }
    public function updateBooking()
    {
        $service = new EmsService();
        $result = collect($service->updateBooking());
        dd($result);
    }

    public function webUsers()
    {
        $service = new EmsService();
        $webUsers = collect($service->getWebUsers());
        dd($webUsers);die();
        foreach ($buildings as $building) {
            echo $building;
        }
    }

    public function groups()
    {
        $service = new EmsService();
        $groups = collect($service->getGroups('ss9545@nyu.edu'));
        dd($groups);die();
    }

    public function groupDetails()
    {
        $service = new EmsService();
        $groupDetails = collect($service->getGroupDetails('67598'));
        dd($groupDetails);die();
    }

    public function findUser(Request $request)
    {
        $inputs= $request->all();
        //dd($inputs);die();
        $NetID = $inputs['NetID'];
        $email = $NetID.'@nyu.edu';
        //dd($NetID);die();
        $service = new EmsService();
        $groupDetails = collect($service->getGroups($email));
        //dd($groupDetails);die();
        return view('input',['inputs'=>$groupDetails]);
    }

    public function userTemplate()
    {

        return Storage::download('/public/userImportTemplates/emsUserImportTemplate.xlsx');
    }
    public function bookingTemplate()
    {

        return Storage::download('/public/userImportTemplates/emsAddBookingsTemplate.xlsx');
    }


    public function testWebUser()
    {
        $service = new EmsService();
        $webUser = collect($service->getWebUsers('rl2896'));
        dd($webUser);

    }
    public function addTmp(Request $request) //需要改进的地方，校验添加的templateID是否已经存在，如果存在就可以不用更新用户信息了
    {
        $inputs = $request->all();
        $webinputNetID = $inputs['NetID'];
        $webinputTemplateID = $inputs['TemplateID'];
        $service = new EmsService();
        $email = $webinputNetID.'@nyu.edu';
        $group = collect($service->getGroups($email));
        //dd($group);die();

        //Verify Web Application User exist? API - getWebUsers
        $webUser = collect($service->getWebUsers($webinputNetID));
        //dd($webUser);die();
        if(!empty($webUser[0]))//User already exist, update web application user via API
        {
            $existWebUserDetails = collect($service->getWebUserDetails($webUser[0]['ID']));
            $existWebUserStatus = $existWebUserDetails[0]['SecurityStatus'];
            $webinputuserName = $webUser[0]['username'];
            if ($existWebUserStatus == 0 | $existWebUserStatus == 3) {
                //Get exist user's application template
                $getExistWebUserAppTemplates = collect($service->getWebUserWebProcessTemplates($webUser[0]['ID']));
                $existWebUserAppTemplateIDs = [];
                foreach ($getExistWebUserAppTemplates as $getExistWebUserAppTemplate) {
                    $existWebUserAppTemplateIDs[] = $getExistWebUserAppTemplate['ID'];
                }
                //Web Application user exist run update
                $existGroupID[] = $group[0]['groupID'];
                //dd($existGroupID);die();
                $existWebUserAppTemplateIDs[] = $webinputTemplateID;
                $updateWebAppTemplates = $existWebUserAppTemplateIDs;
                //dd($updateWebAppTemplates);die();
                $service->updateWebUser($webUser[0]['ID'], $email, $webinputuserName, $webinputNetID, $updateWebAppTemplates, $existGroupID);
                $results['EMS Web Application User'] = 'Update success!';
                $existWebUserDetails = collect($service->getWebUserDetails($webUser[0]['ID']));
                foreach ($existWebUserDetails[0] as $key => $value) {
                    $results['WS_' . $key] = $value;
                }
                $existWebUserAppTemplates = collect($service->getWebUserWebProcessTemplates($webUser[0]['ID']));
                foreach ($existWebUserAppTemplates as $existWebUserAppTemplate) {
                    $results['WS_AppID: ' . $existWebUserAppTemplate['ID']] = $existWebUserAppTemplate['Description'];
                }
                //$results['Bulk Import'] = $path . ' Import Complete!';
                return view('input',['inputs'=>$results]);
            }
            }else {
            //Web Application user status wrong run add to create a new one
            //$results['error'] = 'User not exist!';
            $results['EMS Web Application User'] = 'User not exist!';
            return view('input',['inputs'=>$results]);
        }
    }
    public function bulkImportUser(Request $request)
    {
        //Save uploaded file to $path
        $path = $request->file('emsUpload')->store('/public/emsUpload');
        //Load the just uploaded excel file
        $path1 = storage_path( 'app/' . $path);
        Excel::load($path1, function($reader) {
            $excelDatas = $reader->formatDates(false)->toArray();
            foreach ($excelDatas as $excelData) {
                if(!empty($excelData['username']) && !empty($excelData['netid']) && !empty($excelData['usertype']))
                    {
                        //All required data exist, run create Single User

                        $results = $this->createSingleUser($excelData['netid'],$excelData['username'],$excelData['usertype']);
                        //dd($results);die();
                        if(!empty($results))
                        {
                            //EMS user create success, save log to database
                            $emslog = new Emslogs();
                            $emslog->userName = $excelData['username'];
                            $emslog->NetID = $excelData['netid'];
                            $emslog->userType = $excelData['usertype'];
                            $emslog->eventRequester = $results["ER_Name"];
                            $emslog->webAppUser = $results["WS_username"];
                            $emslog->save();
                        }
                        else
                        {
                            //EMS user created failed, save log to database
                            $emslog = new Emslogs();
                            $emslog->userName = $excelData['username'];
                            $emslog->NetID = $excelData['netid'];
                            $emslog->userType = $excelData['usertype'];
                            $emslog->eventRequester = "Failed";
                            $emslog->webAppUser = "Failed";
                            $emslog->save();
                        }

                    }
                    //Required data missing, save log to database
                    else
                    {
                        $emslog = new Emslogs();
                        $emslog->userName = $excelData['username'];
                        $emslog->NetID = $excelData['netid'];
                        $emslog->userType = $excelData['usertype'];
                        $emslog->eventRequester = "Data Missing";
                        $emslog->webAppUser = "Data Missing";
                        $emslog->save();                    }
            }
        });
        $results['Bulk Import'] = $path . ' Import Complete!';
        return view('input',['inputs'=>$results]);

    }

    public function createUser(Request $request)
    {
        //get user input data, array with User Name, NetID and User Type (Staff, Faculty, Student)
        $inputs= $request->all();
        $webinputNetID = $inputs['NetID'];
        $webinputuserName = $inputs['userName'];
        $webinputuserType = $inputs['userType'];
        $results = $this->createSingleUser($webinputNetID,$webinputuserName,$webinputuserType);
        return view('input',['inputs'=>$results]);

    }

    public function createSingleUser($webinputNetID,$webinputuserName,$webinputuserType)
    {


        //Initialize result for view feedback
        $results = [];

        $webApplicationTemplates = [];
        if($webinputuserType == 'Staff'){
            $webApplicationTemplates = [45,46];
        }elseif($webinputuserType == 'Faculty'){
            $webApplicationTemplates = [45,46,51];
        }elseif($webinputuserType == 'Student'){
            $webApplicationTemplates = [8,38];
        }


        //Verify Event Requester exist? API - getGroups
        $email = $webinputNetID.'@nyu.edu';
        //die();
        //$service = new EmsService();//new EmsService();

        $group = collect($this->service->getGroups($email));
       // var_dump($group);die();

        //Event requester exist, then check if user name and NetID correct API - GetGroupDetails
        if(!empty($group[0])){
            $groupID = $group[0]['groupID'];
            $results['groupID'] = $groupID;
            $groupDetails = collect($this->service->getGroupDetails($groupID));
            //dd($groupDetails);

            //Verify user name, email address and external reference correct in EMS
            if($groupDetails[0]['NetID'] == $webinputNetID
                && $groupDetails[0]['username'] == $webinputuserName. ' ('.$webinputNetID.')'
                && $groupDetails[0]['Email'] == $webinputNetID. '@nyu.edu'
            ){
                //Correct return existing EMS data;
                $results['EMS Event Requester'] = 'Already Exist';
                $results['ER_Name'] = $groupDetails[0]['username'];
                $results['ER_External Reference'] = $groupDetails[0]['NetID'];
                $results['ER_Email Address'] = $groupDetails[0]['Email'];
                //return view('input',['inputs'=>$results]);

            }else{//NOT correct run API - UpdateGroup
                $updateGroup = $this->service->updateGroup($groupID,$webinputNetID.'@nyu.edu',$webinputuserName.' ('.$webinputNetID.')',$webinputNetID);
                if($updateGroup[0]['message'] == 'Success!')//If update success return updated Event Requester details
                    {
                    $updatedGroupDetails = collect($this->service->getGroupDetails($groupID));
                    $results['EMS Event Requester'] = 'Updated';
                    $results['ER_Name'] = $updatedGroupDetails[0]['username'];
                    $results['ER_External Reference'] = $updatedGroupDetails[0]['NetID'];
                    $results['ER_Email Address'] = $updatedGroupDetails[0]['Email'];
                    //return view('input',['inputs'=>$results]);
                    }
                else
                    {
                        $results['EMS Event Requester'] = 'Update FAILED! mailto:shanghai.it.business-application-support@nyu.edu';
                        //return view('input',['inputs'=>$results]);
                    }
            }
        }else{//Event requester NOT exist, then create new event requester API - AddGroup
            //echo "No EMS event requester found";
            $addGroup = $this->service->addGroup($webinputNetID.'@nyu.edu',$webinputuserName.' ('.$webinputNetID.')',$webinputNetID);
            if(!empty($addGroup[0]))
            {
                $addedGroupID = $addGroup[0]['GroupID'];
                $results['groupID'] = $addedGroupID;
                $addedGroupDetails = collect($this->service->getGroupDetails($addedGroupID));
                $results['EMS Event Requester'] = 'Created new';
                $results['ER_Name'] = $addedGroupDetails[0]['username'];
                $results['ER_External Reference'] = $addedGroupDetails[0]['NetID'];
                $results['ER_Email Address'] = $addedGroupDetails[0]['Email'];
                //return view('input',['inputs'=>$results]);
            }else
                {
                    $results['EMS Event Requester'] = 'Create FAILED! mailto:shanghai.it.business-application-support@nyu.edu';
                    //return view('input',['inputs'=>$results]);
                }
        }
        //dd($groupDetails);
        //Verify Web Application User exist? API - getWebUsers
        $webUser = collect($this->service->getWebUsers($webinputNetID));
        if(!empty($webUser[0]))//User already exist, update web application user via API
        {
            $existWebUserDetails = collect($this->service->getWebUserDetails($webUser[0]['ID']));
            //dd($existWebUserDetails);die();
            $existWebUserStatus = $existWebUserDetails[0]['SecurityStatus'];
            if($existWebUserStatus == 0 | $existWebUserStatus == 3)
            {
                //echo 'I am in!';die();
                //Get exist user's application template
                $getExistWebUserAppTemplates = collect($this->service->getWebUserWebProcessTemplates($webUser[0]['ID']));
                $existWebUserAppTemplateIDs = [];
                foreach ($getExistWebUserAppTemplates as $getExistWebUserAppTemplate)
                {
                    $existWebUserAppTemplateIDs[] = $getExistWebUserAppTemplate['ID'];
                }
                //Web Application user exist run update
                $existGroupID = [$results['groupID']];
                $updateWebAppTemplates = array_merge($existWebUserAppTemplateIDs,$webApplicationTemplates);
                //dd($updateWebAppTemplates);die();
                $this->service->updateWebUser($webUser[0]['ID'],$email,$webinputuserName,$webinputNetID,$updateWebAppTemplates,$existGroupID);
                $results['EMS Web Application User'] = 'Update success!';
                $existWebUserDetails = collect($this->service->getWebUserDetails($webUser[0]['ID']));
                foreach ($existWebUserDetails[0] as $key => $value)
                {
                    $results['WS_'.$key] = $value;
                }
                $existWebUserAppTemplates = collect($this->service->getWebUserWebProcessTemplates($webUser[0]['ID']));
                foreach ($existWebUserAppTemplates as $existWebUserAppTemplate)
                {
                    $results['WS_AppID: '.$existWebUserAppTemplate['ID']] = $existWebUserAppTemplate['Description'];
                }
            }else
            {
                //Web Application user status wrong run add to create a new one
                $existGroupID = [$results['groupID']];
                $this->service->addWebUser($email,$webinputuserName,$webinputNetID,$webApplicationTemplates,$existGroupID);
                $results['EMS Web Application User'] = 'Create success!';
                $newWebUser = collect($this->service->getWebUsers($webinputNetID));
                if(!empty($newWebUser[0])){
                    //Search and return new crated Web Application user detail information
                    $newWebUserDetails = $this->service->getWebUserDetails($newWebUser[0]['ID']);
                    foreach ($newWebUserDetails[0] as $key => $value)
                    {
                        $results['WS_'.$key] = $value;
                    }
                    //Search and return new created Web Application user templates detail
                    $newWebUserAppTemplates = $this->service->getWebUserWebProcessTemplates($newWebUser[0]['ID']);
                    foreach ($newWebUserAppTemplates as $newWebUserAppTemplate)
                    {
                        $results['WS_AppID: '.$newWebUserAppTemplate['ID']] = $newWebUserAppTemplate['Description'];
                    }
                }else{
                    $results['EMS Web Application User'] = 'Create FAILED!  mailto:shanghai.it.business-application-support@nyu.edu';
                }
            }
        }else//User not exist, Create new web application user via API
        {
            //Need Group ID to link Web Application User with Event Requester
            $existGroupID = [$results['groupID']];
            $this->service->addWebUser($email,$webinputuserName,$webinputNetID,$webApplicationTemplates,$existGroupID);
            $results['EMS Web Application User'] = 'Create success!';
            $newWebUser = collect($this->service->getWebUsers($webinputNetID));
            if(!empty($newWebUser[0])){
                //Search and return new crated Web Application user detail information
                $newWebUserDetails = $this->service->getWebUserDetails($newWebUser[0]['ID']);
                foreach ($newWebUserDetails[0] as $key => $value)
                {
                    $results['WS_'.$key] = $value;
                }
                //Search and return new created Web Application user templates detail
                $newWebUserAppTemplates = $this->service->getWebUserWebProcessTemplates($newWebUser[0]['ID']);
                foreach ($newWebUserAppTemplates as $newWebUserAppTemplate)
                {
                    $results['WS_AppID: '.$newWebUserAppTemplate['ID']] = $newWebUserAppTemplate['Description'];
                }
            }else{
                $results['EMS Web Application User'] = 'Create FAILED!  mailto:shanghai.it.business-application-support@nyu.edu';
            }
        }
        return $results;

    }
}
