@extends('layouts.bootstrapHead')

@section('content')

    <h1>OrgSync View</h1>
    <h3>Create Single User</h3>
    <form action="getAccountBymail" method="get" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{{csrf_token()}}"/>


        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
    <br>
    <h3>Bulk Upload Users</h3>
    <form action="bulkImportUser" method="post" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{{csrf_token()}}"/>
        <div class="form-group">
            <label for="uploadFile">Please upload new EMS user import template</label>
            <a class="btn btn-outline-info" href="{{url('/emsTemplate')}}" role="button">Download Template</a>
            <input type="file" name="emsUpload" class="form-control-file" id="uploadFile">
        </div>


        <button type="submit" class="btn btn-primary">Upload</button>
    </form>
    <br>
    <h3>Add Web Application Template</h3>
    <form action="addTmp" method="post" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{{csrf_token()}}"/>
        <div class="form-row">

            <div class="form-group col-md-2">
                <label for="NetID">NetID</label>
                <input type="text" required name="NetID" class="form-control" id="inputNetID" placeholder="NetID">
            </div>
            <div class="form-group col-md-2">
                <label for="TemplateID">TemplateID</label>
                <input type="text" required name="TemplateID" value="65" class="form-control" id="inputTemplateID" placeholder="TemplateID">
            </div>


        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
    <br>

    <table class="table">
        <thead>
        <tr>
            <th scope="col">Item</th>
            <th scope="col">Value</th>
            <th scope="col">Date</th>
            <th scope="col">Time Start</th>
            <th scope="col">Time End</th>
            <th scope="col">Classroom</th>
        </tr>
        </thead>

        @if(!empty($inputs))
            <h1>Import Result</h1>
        @foreach($inputs as $key => $value )
                <tbody>
                <tr>
                    <td>{{$key}}</td>
                    <td>{{$inputs[$key]}}</td>

                </tr>

                </tbody>
            @endforeach
        @endif
    </table>
    <h1>End of TABLE</h1>



@endsection