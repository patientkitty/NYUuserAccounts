@extends('layouts.bootstrapHead')

@section('content')

    <h1>New Schedule Form</h1>
    <form action="add" method="post" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{{csrf_token()}}"/>
        <div class="form-row">
            <div class="form-group col-md-1">
                <label for="inputGroup">Group</label>
                <input type="number" name="group" class="form-control" id="inputGroup" placeholder="#" min="1" max="100">
            </div>
            <div class="form-group col-md-2">
                <label for="inputCourseName">Course Name</label>
                <input type="text" name="event_name" class="form-control" id="inputCourseName" placeholder="Event Name">
            </div>
            <div class="form-group col-md-2">
                <label for="inputDateStart">Date Start</label>
                <input id="inputDateStart" name="date_start" type="tel" required
                       pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" class="form-control" placeholder="yyyy-mm-dd">
            </div>
            <div class="form-group col-md-2">
                <label for="inputDateEnd">Date End</label>
                <input id="inputDateEnd" name="date_end" type="tel" required
                       pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" class="form-control" placeholder="yyyy-mm-dd">
            </div>
            <div class="form-group col-md-1">
                <label for="inputTimeStart">Time Start</label>
                <input id="inputTimeStart" name="time_start" type="tel" required
                       pattern="[0-9]{4}" class="form-control" placeholder="hhmm">
            </div>
            <div class="form-group col-md-1">
                <label for="inputTimeEnd">Time End</label>
                <input id="inputTimeEnd" name="time_end" type="tel" required
                       pattern="[0-9]{4}" class="form-control" placeholder="hhmm">
            </div>
            <div class="form-group col-md-1">
                <label for="inputWeekday">Weekday</label>
                <input id="inputWeekday" name="weekdays" type="tel" required
                       pattern="[1-7]{1}" list="defaultWeekdays" placeholder="#" class="form-control">
                <datalist id="defaultWeekdays">
                    <option value="1">
                    <option value="2">
                    <option value="3">
                    <option value="4">
                    <option value="5">
                    <option value="6">
                    <option value="7">
                </datalist>
            </div>
            <div class="form-group col-md-1">
                <label for="inputClassroom">Classroom</label>
                <input type="text" name="room" class="form-control" id="inputClassroom" placeholder="#">
                <!--<input id="inputClassroom" name="room" type="tel" required
                       pattern="[0-9]{4}" class="form-control" placeholder="hhmm">-->
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
    <br>

    <table class="table">
        <thead>
        <tr>
            <th scope="col">Group</th>
            <th scope="col">Event Name</th>
            <th scope="col">Date</th>
            <th scope="col">Time Start</th>
            <th scope="col">Time End</th>
            <th scope="col">Classroom</th>
        </tr>
        </thead>

        @if(!empty($inputs))
            @foreach($inputs as $input)
                <tbody>
                <tr>
                    <td>{{$input->group}}</td>
                    <td>{{$input->event_name}}</td>
                    <td>{{$input->date}}</td>
                    <td>{{$input->time_start}}</td>
                    <td>{{$input->time_end}}</td>
                    <td>{{$input->room}}</td>
                </tr>

                </tbody>
            @endforeach
        @endif
    </table>
    <h1>End of TABLE</h1>



@endsection