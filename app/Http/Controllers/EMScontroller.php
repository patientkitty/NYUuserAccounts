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

    public function createUser(Request $request)
    {
        //Initialize result for view feedback
        $results = [];

        //get user input data, array with User Name, NetID and User Type (Staff, Faculty, Student)
        $inputs= $request->all();
        $webinputNetID = $inputs['NetID'];
        $webinputuserName = $inputs['userName'];
        $webinputuserType = $inputs['userType'];

        //Verify Event Requester exist? API - getGroups
        $email = $webinputNetID.'@nyu.edu';
        $service = new EmsService();
        $group = collect($service->getGroups($email));

        //Event requester exist, then check if user name and NetID correct API - GetGroupDetails
        if(!empty($group[0])){
            $groupID = $group[0]['groupID'];
            $groupDetails = collect($service->getGroupDetails($groupID));
            //dd($groupDetails);

            //Verify user name, email address and external reference correct in EMS
            if($groupDetails[0]['NetID'] == $webinputNetID
                && $groupDetails[0]['username'] == $webinputuserName. ' ('.$webinputNetID.')'
                && $groupDetails[0]['Email'] == $webinputNetID. '@nyu.edu'
            ){
                //Correct return existing EMS data;
                $results['EMS Event Request'] = 'Already Exist';
                $results['ER_Name'] = $groupDetails[0]['username'];
                $results['ER_External Reference'] = $groupDetails[0]['NetID'];
                $results['ER_Email Address'] = $groupDetails[0]['Email'];
                return view('input',['inputs'=>$results]);
                //dd($result);
            }else{//NOT correct run API - UpdateGroup
                echo 'something wrong';
            }

        }else{//Event requester NOT exist, then create new event requester API - AddGroup
            echo "No EMS event requester found";
        }
        //dd($groupDetails);

    }
}
