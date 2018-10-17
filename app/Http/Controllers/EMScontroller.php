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

    public function findUser($email)
    {
        $service = new EmsService();
        $groupDetails = collect($service->getGroups($email));
        dd($groupDetails);die();
    }
}
