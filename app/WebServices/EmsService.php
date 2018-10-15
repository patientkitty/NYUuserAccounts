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

        //$wsdl = file_get_contents($url);
        // libxml_disable_entity_loader(false);
        $context = stream_context_create([
            "ssl" => [
                "verify_peer" => false,
                "allow_self_signed" => true,
                'verify_peer_name' => false,
            ],

        ]);

        $this->client = new \SoapClient($url, [
            'stream_context' => $context
        ]);
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
            // 'Buildings' => (int)$id,
        ]);
        $roomsResult = $result->GetRoomsResult;
        //  var_dump($result);
        $oXML = new \SimpleXMLElement($roomsResult);
        $rooms = [];
        $xml_rooms = $oXML->Data;
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

    public function getWebUsers()
    {

        $result = $this->client->GetWebUsers([
            'UserName' => $this->username,
            'Password' => $this->password,
            'ExternalReference' => 'ss9545',
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
                //'room' => $xml_building->Room,
            ];
            $webUsers[] = $webUser;
        }
        return $webUsers;

    }
}