<?php
/**
 * Created by PhpStorm.
 * User: ss9545
 * Date: 14/11/2018
 * Time: 5:57 PM
 */

namespace App\WebServices;

use GuzzleHttp\Client;

class OrgSyncService
{
    private $key = 'CYy5USawtasNFaGAdKRVag';

    public function getSingleForm($formid)
    {
        $client = new Client();
        $url = 'https://api.orgsync.com/api/v2/forms/'.$formid.'?format=json&key='.$this->key;
        $response = $client->get($url);
        $form = json_decode($response->getBody(), true);
        return $form;
        // var_dump($result);
    }
    public function getSingleSubmission($submission_id)
    {
        $client = new Client();
        $url = 'https://api.orgsync.com/api/v2/form_submissions/'.$submission_id.'?format=json&key='.$this->key;
        $response = $client->get($url);
        $submission = json_decode($response->getBody(), true);
        return $submission;
    }

}
