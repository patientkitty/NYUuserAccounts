<?php
/**
 * Created by PhpStorm.
 * User: ss9545
 * Date: 26/09/2018
 * Time: 2:46 PM
 */
namespace App\WebServices;

class EmsService
{

    private $client, $username, $password;

    public function __construct()
    {
        $url = config('ems.url');
        $this->username = config('ems.username');
        $this->password = config('ems.password');
        $this->grouptypeid = config('ems.grouptypeid');
        $this->active = config('ems.active');
        $this->timezoneid = config('ems.timezoneid');
        $this->webSecurityTemplateID = config('ems.webSecurityTemplateID');

        //$wsdl = file_get_contents($url);
        // libxml_disable_entity_loader(false);
        $context = stream_context_create([
            "ssl" => [
                "verify_peer" => false,
                "allow_self_signed" => true,
                'verify_peer_name' => false,
            ],

        ]);
        $opts = array(
            /*'http' => array(
                'user_agent' => 'PHPSoapClient'
            ),*/
            'cache_wsdl' => 0,
            'trace' => 1,
            'stream_context' => stream_context_create(array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                )
            )

        );
        //$context = stream_context_create($opts);

        //$this->client = new \SoapClient($url);
        //$data = file_get_contents($url);
        //var_dump($data);die();

        $this->client = new \SoapClient($url, $opts);
    }
    public function updateBooking()
    {
        $result = $this->client->UpdateBooking([
            'UserName' => $this->username,
            'Password' => $this->password,
            'BookingDate' =>  (string)"10/2/2019",
            'StartTime' => "10/02/2019 9:45 AM",
            'EndTime' => "10/02/2019 11:00 AM",
            'RoomID' => 524,
            'BookingID' => 213313,
            'StatusID' => 0,
        ]);
        $updateBookingResponse = $result->UpdateBookingResponse;
        $oXML = new \SimpleXMLElement($updateBookingResponse);
        var_dump($oXML);
        //$buildings = [];
    }
    public function addReservation($groupID,$roomID,$bookingDate,$startTime,$endTime,$statusID,$eventName)
    {
       // echo "book";
        $result = $this->client->AddReservation([
            'UserName' => $this->username,
            'Password' => $this->password,
            'GroupID' => $groupID,
            'RoomID' => $roomID,
            'BookingDate' =>  (string)$bookingDate,
            'StartTime' => (string)$startTime,
            'EndTime' => (string)$endTime,
            'StatusID' => $statusID,
            'EventName' => $eventName,

//              Add Reservation sample data
//            'GroupID' => 48956,
//            'RoomID' => 301,
//            'BookingDate' =>  (string)"2020-08-04T00:00:00",
//            'StartTime' => (string)"2020-08-04T11:00:00",
//            'EndTime' => (string)"2020-08-04T11:10:00",
//            'StatusID' => 1,
//            'EventName' => "Created from API",

        ]);

        //AddReservationResult AddReservationResponse
        $addReservationResponse = $result->AddReservationResult;
        $oXML = new \SimpleXMLElement($addReservationResponse);
        //var_dump($oXML);die();
        $xml_addReservationResult = $oXML->Data;
        $xml_addReservationError = $oXML->Error;
        $result = [];
        $result['newReservationID'] = (string)$xml_addReservationResult->ReservationID;
        $result['newBookingID'] = (string)$xml_addReservationResult->BookingID;
        $result['message'] = (string)$xml_addReservationError->Message;
        return $result;
    }
    public function addBooking($resevationID,$roomID,$bookingDate,$startTime,$endTime,$statusID,$eventName,$eventTypeID)
    {

        $result = $this->client->AddBooking([
            'UserName' => $this->username,
            'Password' => $this->password,
            'ReservationID' => $resevationID,
            'RoomID' => $roomID,
            'BookingDate' =>  (string)$bookingDate,
            'StartTime' => (string)$startTime,
            'EndTime' => (string)$endTime,
            'StatusID' => $statusID,
            'EventName' => $eventName,
            'EventTypeID' => $eventTypeID,
//              Add Booking sample data
//            'ReservationID' => 44471,
//            'RoomID' => 101,
//            'BookingDate' =>  (string)"2020-07-24T00:00:00",
//            'StartTime' => (string)"2020-07-24T15:13:00",
//            'EndTime' => (string)"2010-07-24T18:13:00",
//            'StatusID' => 1,
//            'EventName' => "Created from Admin Client",
//            'EventTypeID' => 10,

        ]);
        //AddBookingResult AddBookingResponse
        $addBookingResponse = $result->AddBookingResult;
        $oXML = new \SimpleXMLElement($addBookingResponse);
        //var_dump($oXML);die();
        $xml_addBookingResult = $oXML->Data;
        $xml_addBookingError = $oXML->Error;
        $result = [];
        $result['newBookingID'] = (string)$xml_addBookingResult->BookingID;
        $result['message'] = (string)$xml_addBookingError->Message;
        return $result;
        //dd($newBookingID);
        //$buildings = [];
    }

    public function getBuildings()
    {

        $result = $this->client->GetBuildings([
            'UserName' => $this->username,
            'Password' => $this->password,
        ]);
        $buildingsResult = $result->GetBuildingsResult;
        $oXML = new \SimpleXMLElement($buildingsResult);
        //var_dump($oXML);
        $buildings = [];
        $xml_buildings = $oXML->Data;
        foreach ($xml_buildings as $xml_building) {
            $building = [
                'building_id' => (string)$xml_building->ID,
                'description' => (string)$xml_building->Description,
                //'room' => $xml_building->Room,
            ];
            $buildings[] = $building;
        }
        return $buildings;

    }

    public function getRoomsByBuilding($id)
    {
        $result = $this->client->GetRooms([
            'UserName' => $this->username,
            'Password' => $this->password,
            'Buildings' => [11],
        ]);
        $roomsResult = $result->GetRoomsResult;
        //  var_dump($result);
        $oXML = new \SimpleXMLElement($roomsResult);

        $rooms = [];
        $xml_rooms = $oXML->Data;
        //var_dump($xml_rooms);die();
        foreach ($xml_rooms as $xml_room) {
            $building_id = (int)$xml_room->BuildingID;
            if ($building_id == $id) {
                $room = [
                    'room' => (string)$xml_room->Room,
                    'description' => (string)$xml_room->Description,
                    'room_id' => (string)$xml_room->ID,
                    'building' => (string)$xml_room->Building,
                    'building_id' => (string)$building_id,
                ];
                $rooms[] = $room;
            }
            //var_dump($xml_room->BuildingID)

        }
        return $rooms;

    }

    public function getRoomBooking($startDate, $endDate, $roomID)
    {
        $result = $this->client->GetRoomBookings([
            'UserName' => $this->username,
            'Password' => $this->password,
            'StartDate' => (string)$startDate,
            'EndDate' => (string)$endDate,
            'RoomID' => (int)$roomID,
            'ViewComboRoomComponents' => 1,
        ]);
        $bookingResult = $result->GetRoomBookingsResult;
        $oXML = new \SimpleXMLElement($bookingResult);
        $xml_bookings = $oXML->Data;
        $bookings = [];
        foreach ($xml_bookings as $xml_booking) {
            $status = (int)$xml_booking->StatusID;
            if ($status == 1 || $status == 2 || $status == 11) {
                $booking = [
                    'start' => (string)$xml_booking->TimeEventStart,
                    'end' => (string)$xml_booking->TimeEventEnd,
                    'name' => (string)$xml_booking->EventName,
                    'status' => $status
                ];
                $bookings[] = $booking;
            }

        }
        return $bookings;
    }

    public function getWebUsers($NetID)
    {

        $result = $this->client->GetWebUsers([
            'UserName' => $this->username,
            'Password' => $this->password,
            'ExternalReference' => $NetID,
        ]);
        $webUsersResult = $result->GetWebUsersResult;
        $oXML = new \SimpleXMLElement($webUsersResult);
        //var_dump($oXML);
        $webUsers = [];
        $xml_webUsers = $oXML->Data;
        foreach ($xml_webUsers as $xml_webUser) {
            $webUser = [
                'username' => (string)$xml_webUser->UserName,
                'NetID' => (string)$xml_webUser->ExternalReference,
                'ID' => (string)$xml_webUser->ID,
                //'room' => $xml_building->Room,
            ];
            $webUsers[] = $webUser;
        }
        return $webUsers;

    }

    public function getWebUserDetails($WebUserID)
    {

        $result = $this->client->GetWebUserDetails([
            'UserName' => $this->username,
            'Password' => $this->password,
            'WebUserID' => $WebUserID,
        ]);
        $webUserDetailsResult = $result->GetWebUserDetailsResult;
        $oXML = new \SimpleXMLElement($webUserDetailsResult);
        //var_dump($oXML);
        $webUserDetails = [];
        $xml_webUserDetails = $oXML->Data;
        foreach ($xml_webUserDetails as $xml_webUserDetail) {
            $webUserDetail = [
                'username' => (string)$xml_webUserDetail->UserName,
                'NetID' => (string)$xml_webUserDetail->ExternalReference,
                'Email' => (string)$xml_webUserDetail->EmailAddress,
                'NetworkID' => (string)$xml_webUserDetail->NetworkID,
                'TimeZoneID' => (string)$xml_webUserDetail->TimeZoneID,
                'SecurityStatus' => (string)$xml_webUserDetail->SecurityStatus,
                'SecTemplateID' => (string)$xml_webUserDetail->TemplateID,
                //'room' => $xml_building->Room,
            ];
            $webUserDetails[] = $webUserDetail;
        }
        return $webUserDetails;

    }

    public function getWebUserWebProcessTemplates($WebUserID)
    {

        $result = $this->client->GetWebUserWebProcessTemplates([
            'UserName' => $this->username,
            'Password' => $this->password,
            'WebUserID' => $WebUserID,
        ]);
        $webUserWebProcessTemplates =[];
        $webUserWebProcessTemplatesResult = $result->GetWebUserWebProcessTemplatesResult;
        $oXML = new \SimpleXMLElement($webUserWebProcessTemplatesResult);
        $xml_webUserWebProcessTemplates = $oXML->Data;
        foreach ($xml_webUserWebProcessTemplates as $xml_webUserWebProcessTemplate) {
            $webUserWebProcessTemplate = [
                'ID' => (string)$xml_webUserWebProcessTemplate->ID,
                'Description' => (string)$xml_webUserWebProcessTemplate->Description,
            ];
            $webUserWebProcessTemplates[] = $webUserWebProcessTemplate;
        }
        return $webUserWebProcessTemplates;

    }

    public function getGroups($email)
    {

        $result = $this->client->GetGroups([
            'UserName' => $this->username,
            'Password' => $this->password,
            'EmailAddress' => $email,
        ]);
        $groupResult = $result->GetGroupsResult;
        $oXML = new \SimpleXMLElement($groupResult);
        //var_dump($oXML);
        $groups = [];
        $xml_groups = $oXML->Data;
        foreach ($xml_groups as $xml_group) {
            $group = [
                'username' => (string)$xml_group->GroupName,
                'Email' => (string)$xml_group->EMailAddress,
                'groupID' => (string)$xml_group->ID,
                //'room' => $xml_building->Room,
            ];
            $groups[] = $group;
        }
        return $groups;
    }

    public function getGroupDetails($groupID)
    {

        $result = $this->client->GetGroupDetails([
            'UserName' => $this->username,
            'Password' => $this->password,
            'GroupID' => $groupID,
        ]);
        $groupDetailResult = $result->GetGroupDetailsResult;
        $oXML = new \SimpleXMLElement($groupDetailResult);
        $groupDetails = [];
        $xml_groupDetails = $oXML->Data;
        foreach ($xml_groupDetails as $xml_groupDetail) {
            $groupDetail = [
                'username' => (string)$xml_groupDetail->GroupName,
                'NetID' => (string)$xml_groupDetail->ExternalReference,
                'Email' => (string)$xml_groupDetail->EMailAddress,

            ];
            $groupDetails[] = $groupDetail;
        }
        return $groupDetails;
    }

    public function updateGroup($groupID,$email,$username,$NetID)
    {

        $result = $this->client->UpdateGroup([
            'UserName' => $this->username,
            'Password' => $this->password,
            'GroupTypeID' => $this->grouptypeid,
            'Active' =>  $this->active,
            'GroupID' => $groupID,
            'EmailAddress' => $email,
            'Fax' => '',
            'GroupName' => $username,
            'ExternalReference' => $NetID,
            'Address1' => '',
            'Address2' => '',
            'City' => '',
            'State' => '',
            'ZipCode' => '',
            'Country' => '',
            'Phone' => ''
        ]);
        $updateGroup = $result->UpdateGroupResult;
        $oXML = new \SimpleXMLElement($updateGroup);
        $updateGroupResults = [];
        $xml_updateGroupresults = $oXML->Message;
        foreach ($xml_updateGroupresults as $xml_updateGroupresult) {
            $updateGroupResult = [
                'message' => (string)$xml_updateGroupresult->Message,
            ];
            $updateGroupResults[] = $updateGroupResult;
        }
        return $updateGroupResults;
    }


    public function addGroup($email,$username,$NetID)
    {

        $result = $this->client->AddGroup([
            'UserName' => $this->username,
            'Password' => $this->password,
            'GroupTypeID' => $this->grouptypeid,
            'EmailAddress' => $email,
            'Fax' => '',
            'GroupName' => $username,
            'ExternalReference' => $NetID,
            'Address1' => '',
            'Address2' => '',
            'City' => '',
            'State' => '',
            'ZipCode' => '',
            'Country' => '',
            'Phone' => ''
        ]);
        $addGroup = $result->AddGroupResult;
        $oXML = new \SimpleXMLElement($addGroup);
        $addGroupResults = [];
        $xml_addGroupresults = $oXML->Data;
        foreach ($xml_addGroupresults as $xml_addGroupresult) {
            $addGroupResult = [
                'GroupID' => (string)$xml_addGroupresult->GroupID,
            ];
            $addGroupResults[] = $addGroupResult;
        }
        return $addGroupResults;
    }

    public function addWebUser($email,$username,$NetID,$webAppTemplates,$groupID)
    {

        $result = $this->client->AddWebUser([
            'UserName' => $this->username,
            'Password' => $this->password,
            'EmailAddress' => $email,
            'Fax' => '',
            'TimeZoneID' => $this->timezoneid,
            'StatusID' => 0,
            'WebSecurityTemplateID' => $this->webSecurityTemplateID,
            'WebUserName' => $username,
            'ExternalReference' => $NetID,
            'NetworkID' => $NetID,
            'TimeZoneID' => $this->timezoneid,
            'StatusID' => 0,
            'WebSecurityTemplateID' => $this->webSecurityTemplateID,
            'WebProcessTemplates' => $webAppTemplates,
            'Groups' =>  $groupID,
            'Validated' => 1,
            'Phone' => '',
        ]);
        //dd($result);die();
        $addWebUser = $result->AddWebUserResult;
        $oXML = new \SimpleXMLElement($addWebUser);
        $addWebUserResults = [];
        $xml_addWebUserresults = $oXML->Data;
        foreach ($xml_addWebUserresults as $xml_addWebUserresult) {
            $addWebUserResult = [
                'WebUserID' => (string)$xml_addWebUserresult->WebUserID,
            ];
            $addWebUserResults[] = $addWebUserResult;
        }
        return $addWebUserResults;
    }

    public function updateWebUser($webUserID,$email,$username,$NetID,$webProcessTemplates,$groupIDs)
    {

        $result = $this->client->UpdateWebUser([
            'UserName' => $this->username,
            'Password' => $this->password,
            'WebUserID' => $webUserID,
            'WebUserName' =>  $username,
            'EmailAddress' => $email,
            'Fax' => '',
            'NetworkID' => $NetID,
            'ExternalReference' => $NetID,
            'TimeZoneID' => $this->timezoneid,
            'StatusID' => 0,//0 means active user
            'WebSecurityTemplateID' => $this->webSecurityTemplateID,
            'WebProcessTemplates' => $webProcessTemplates,
            'Groups' => $groupIDs,
            'Validated' => 1,
            'Phone' => '',
        ]);
        $updateWebUser = $result->UpdateWebUserResult;
        $oXML = new \SimpleXMLElement($updateWebUser);
        $updateWebUserResults = [];
        $xml_updateWebUserresults = $oXML->Message;
        foreach ($xml_updateWebUserresults as $xml_updateWebUserresult) {
            $updateWebUserResult = [
                'message' => (string)$xml_updateWebUserresult->Message,
            ];
            $updateWebUserResults[] = $updateWebUserResult;
        }
        return $updateWebUserResults;
    }
}