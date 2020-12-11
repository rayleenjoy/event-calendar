@extends('layouts.master')
@section('title', 'Event Calendar')

@section('content')
    <main class="content">
        <legend>Calendar</legend>
        <div class="row">
            <div class="col-md-4">
                <form action="{{route('event')}}" method="POST" id="eventForm" autocomplete="off">
                    @csrf
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label for="">Event</label>
                            <input type="text" class="form-control" name="event" >
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="">From</label>
                            <input type="text" class="form-control datepicker" name="from">
                        </div>
                        <div class="col-md-6">
                            <label for="">To</label>
                            <input type="text" class="form-control datepicker" name="to">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <input type="checkbox" name="days[]" value="1"> Mon 
                            <input type="checkbox" name="days[]" value="2"> Tue 
                            <input type="checkbox" name="days[]" value="3"> Wed
                            <input type="checkbox" name="days[]" value="4"> Thu 
                            <input type="checkbox" name="days[]" value="5"> Fri 
                            <input type="checkbox" name="days[]" value="6"> Sat
                            <input type="checkbox" name="days[]" value="0"> Sun
                        </div>
                    </div>
                    <button class="btn btn-primary" type="submit"> Save</button>
                </form>
            </div>
            <div class="col-md-8">
                <div id="calendar">
                    <table id="tbl_content" width="100%" class="table">
                        <thead>
                        <th colspan="2"><h4 id="monthYear"></h4></th>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

@endsection

@section('plugins-script')
<script>
    $(".datepicker").datepicker();

    $("#eventForm").submit(function(event){
        event.preventDefault(); 

        $.ajax({
            url : $(this).attr("action"),
            type: $(this).attr("method"),
            data : $(this).serialize()
        }).done(function(response){
            if(response.success){
                displayCalendar(response.data.dates[0]);
                var result = response.data.dates;
                var event = response.data.event_name;
                for(i = 0; i < result.length; i++){
                    var date_id = result[i];
                    $('#tbl_content tbody tr#date_'+date_id).addClass('highlight');
                    $('#tbl_content tbody #date_'+date_id).find("td:eq(1)").text(event);
                }
                toastr.success('Event Successfully Added.');
            }else{
                var error = '';
                var message = response.message;
                for(i = 0; i < message.length; i++){
                    error += '<li>'+message[i]+'</li>';
                }
                toastr.error('<ul>'+error+'<ul>');
            }
        }).fail(function(response){
          alert("Please reload page and try again.");
        });

    });

    function displayCalendar(eventDate='')
    {
        $('#tbl_content tbody').html('');
        var week_of_days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thur', 'Fri', 'Sat'];
        var monthName = ['Jan', 'Feb', 'March', 'April', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];
        var date = new Date();
        if(eventDate){
            var date = new Date(eventDate);
        }
        var month = date.getMonth()+1;
        if(month < 10){
            month = '0'+month;
        }
        var year = date.getFullYear();
        var end = new Date(year, month, 0).getDate(); 
        var tbody = '';
        for(i = 1; i <= end; i++){
           var date_id = year + '-' + month +'-'+ (i < 10 ? '0'+ i: i);
           var day = new Date(date_id).getDay();
           var display = i + ' ' +week_of_days[day];
           tbody += '<tr id="date_'+date_id+'"><td>'+display+'</td><td></td></tr>';
        }
        $('#tbl_content #monthYear').text(monthName[month-1] + ' ' + year);
        $('#tbl_content tbody').append(tbody);
    }

    displayCalendar();

</script>

@stop