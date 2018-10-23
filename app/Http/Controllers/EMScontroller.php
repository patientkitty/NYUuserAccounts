<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\WebServices\EmsService;

class EMScontroller extends Controller
{
    //
    public function hello()
    {
        echo "hello";
        $key = config('app.key');
        echo $key;
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

    public function test()
    {
        $service = new EmsService();
        $run = $service->getWebUserWebProcessTemplates('91276');
        $results = [];
        foreach ($run as $ru)
        {
            $results['WS_AppID: '.$ru['ID']] = $ru['Description'];
        }
        return view('input',['inputs'=>$results]);
        //dd($run);
    }

    public function createUser(Request $request)
    {
        //Initialize result for view feedback
        $results = [];

        //get user input data, array with User Name, NetID and User Type (Staff, Faculty, Student)
        $inputs= $request->all();
        $webinputNetID = $inputs['NetID'];
        $webinputuserName = $inputs['userName'];
        $webinputuserType = $inputs['userType'];
        $webApplicationTemplates = [];
        if($webinputuserType == 'Staff'){
            $webApplicationTemplates = [45,46];
        }elseif($webinputuserType == 'Faculty'){
            $webApplicationTemplates = [45,46,51];
        }elseif($webinputuserType == 'Student'){
            $webApplicationTemplates = [8,38];
        }
        //dd($webApplicationTemplates);die();

        //Verify Event Requester exist? API - getGroups
        $email = $webinputNetID.'@nyu.edu';
        $service = new EmsService();
        $group = collect($service->getGroups($email));

        //Event requester exist, then check if user name and NetID correct API - GetGroupDetails
        if(!empty($group[0])){
            $groupID = $group[0]['groupID'];
            $results['groupID'] = $groupID;
            $groupDetails = collect($service->getGroupDetails($groupID));
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
                $updateGroup = $service->updateGroup($groupID,$webinputNetID.'@nyu.edu',$webinputuserName.' ('.$webinputNetID.')',$webinputNetID);
                if($updateGroup[0]['message'] == 'Success!')//If update success return updated Event Requester details
                    {
                    $updatedGroupDetails = collect($service->getGroupDetails($groupID));
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
            $addGroup = $service->addGroup($webinputNetID.'@nyu.edu',$webinputuserName.' ('.$webinputNetID.')',$webinputNetID);
            if(!empty($addGroup[0]))
            {
                $addedGroupID = $addGroup[0]['GroupID'];
                $results['groupID'] = $addedGroupID;
                $addedGroupDetails = collect($service->getGroupDetails($addedGroupID));
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
        $webUser = collect($service->getWebUsers($webinputNetID));
        if(!empty($webUser[0]))//User already exist, update web application user via API
        {
            dd($webUser); die();
        }else//User not exist, Create new web application user via API
        {
            //Need Group ID to link Web Application User with Event Requester
            $existGroupID = [$results['groupID']];
            $service->addWebUser($email,$webinputuserName,$webinputNetID,$webApplicationTemplates,$existGroupID);
            $results['EMS Web Application User'] = 'Create success!';
            $newWebUser = collect($service->getWebUsers($webinputNetID));
            if(!empty($newWebUser[0])){
                //Search and return new crated Web Application user detail information
                $newWebUserDetails = $service->getWebUserDetails($newWebUser[0]['ID']);
                foreach ($newWebUserDetails[0] as $key => $value)
                {
                    $results['WS_'.$key] = $value;
                }
                //Search and return new created Web Application user templates detail
                $newWebUserAppTemplates = $service->getWebUserWebProcessTemplates($newWebUser[0]['ID']);
                foreach ($newWebUserAppTemplates as $newWebUserAppTemplate)
                {
                    $results['WS_AppID: '.$newWebUserAppTemplate['ID']] = $newWebUserAppTemplate['Description'];
                }
            }else{
                $results['EMS Web Application User'] = 'Create FAILED!  mailto:shanghai.it.business-application-support@nyu.edu';
            }
        }
        return view('input',['inputs'=>$results]);

    }
}
