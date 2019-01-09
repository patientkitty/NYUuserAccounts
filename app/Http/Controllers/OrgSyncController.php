<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\WebServices\OrgSyncService;
use Maatwebsite\Excel\Excel;
use App\Models\EMSuserUpload;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use App\Exports\EMSExport;


class OrgSyncController extends Controller
{
    //
    private $excel;

    public function __construct(Excel $excel)
    {
        $this->excel = $excel;
    }

    public function test1()
    {
        echo "new controller";
        //$array = [38749890, 38750017, 38750017, 38962235, 38962296, 38962450, 38978681, 38837553, 38749936, 38750703, 38962382, 38749962, 38749978, 38751209, 38977168, 38751030, 38798193, 38963238, 38750354, 38751797, 38962581, 38749898, 38890352, 38890774, 38750012, 38749864, 38962647, 38750085, 38962256, 38750327, 38998715, 38964162, 38771294, 38981934, 38962344, 38962569, 38981932, 38750157, 38769886, 38962268];
        $array = [38749890, 38750017];
        $results = [];
        foreach ($array as $run){
            $service = new OrgSyncService();
            $runing = $service->getSingleSubmission($run);
            $value = [];
            //$results['SubmissionID'] = $run['id'];
            $responses = $runing['responses'];
            foreach ($responses as $respons) {
                //$subject = $respons['element']['name'];
                if ($respons['element']['id'] == 4650185){
                    if(is_array($respons['data'])){
                        $value = $respons['data']['name'];
                    }else{
                        $value = $respons['data'];
                    }
                }
                //if data is an array
                $subject = $runing['id'];
                $results[$subject] = $value;
            }
        }
        $exceldata = EMSuserUpload::all();
        return $this->excel->download(new EMSExport(), 'users.csv');
        //return Excel::download(new EMSExport(), 'users.xls');
        //$results['sam']='yes';
        //dd($responses);
        dd($results);
        die();
    }
}
