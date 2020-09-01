<?php
/**
 * Created by PhpStorm.
 * User: ss9545
 * Date: 26/09/2018
 * Time: 2:46 PM
 */

namespace App\WebServices;
use function GuzzleHttp\_current_time;
use GuzzleHttp\Client;
use App\Models\LocalUDWToken;


class LocalUDWService
{

    private $currentToken, $client, $username, $password;

    public function __construct()
    {
        //get a validate token
        $id = LocalUDWToken::max('id');
        //Tonken alread in DB
        if(!empty($id)){
            $expireIn = LocalUDWToken::where('id', '=', $id)->value('expires_in');
            //get the difference in second of current token in DB
            $diffInSec =  LocalUDWToken::where('id', '=', $id)->value('created_at')->diffInSeconds(now(), false);
            //Token已经超时，需要获取新的
            if($diffInSec > $expireIn){
                $this->requestToken();
                $newid = LocalUDWToken::max('id');
                $this->currentToken = LocalUDWToken::where('id', '=', $newid)->value('access_token');
            }else{//Token没有超时，可以直接使用数据库里存放的值
                $this->currentToken = LocalUDWToken::where('id', '=', $id)->value('access_token');
            }

            $this->currentToken = LocalUDWToken::where('id', '=', $id)->value('access_token');
        }else{//数据库里没有任何Token，直接获取一个新的
            $this->requestToken();
            $newid = LocalUDWToken::max('id');
            $this->currentToken = LocalUDWToken::where('id', '=', $newid)->value('access_token');
        }
        //获取一个有效的token完毕

    }

    public function requestToken(){
        $http = new Client();

        $response = $http->post('http://10.214.22.184/oauth/token', [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => '14',
                'client_secret' => 'ApMdcQSG9ZUp0N1ggQPxzA2Mua9Xi3HsL0gR9NW7',
                'username' => 'admin',
                'password' => 'Nyushanghai!',
                //'scope' => '',
            ],
        ]);

        $token = json_decode((string) $response->getBody(), true);
        $newToken = new LocalUDWToken();
        foreach ($token as $key => $value) {

            $newToken->$key = $token[$key];
        }
        $newToken->save();
        $newid = LocalUDWToken::max('id');
        $this->currentToken = LocalUDWToken::where('id', '=', $newid)->value('access_token');
    }

    public function getUserByNetID(){


        $http = new Client();
        try{
            $netID = 'ss9545';
            $response = $http->request('GET', 'http://10.214.22.184/api/user/'.$netID, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.$this->currentToken,
                    //'Authorization' => 'Bearer ',
                ],
            ]);
            $data = json_decode((string) $response->getBody(), true);
            return $data;
        }
        catch (\Exception $exception)
        {
            echo $exception->getMessage();
        }
        return null;

    }




}