<?php

namespace App\Http\Controllers;

use DB;
use App\CalendarEvent;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventsController extends Controller
{
    public function index()
    {
    	return view('home');
    }

    public function store(Request $request)
    {
    	$validator = Validator::make($request->all(), [
    		'event' => 'required',
    		'from' => 'required|date',
    		'to' => 'required|date'
    	]);

    	if ($validator->fails()) {
            return $this->response(false, [], $validator->errors()->all());
        }
        $eventDates = $this->datesBetween($request);

        if(empty($eventDates)){
            return $this->response(false, [], ['No Events Created.']);
        }

    	DB::beginTransaction();
    	try {

			$data = new CalendarEvent;
    		$data->event_name = $request->event;
    		$data->date_from = date('Y-m-d', strtotime($request->from));
    		$data->date_to = date('Y-m-d', strtotime($request->to));
    		$data->days = json_encode($request->days);
			$data->save();	    		

    		$event_data = [
                'dates' => $eventDates,
                'event_name' => $data->event_name
            ];

    		DB::commit();
    		return $this->response(true, $event_data);

    	} catch (Exception $e) {
    		DB::rollBack();
    		return $this->response(false, [], [$e->getMessage()]);
    	}
    }

    public function datesBetween($data)
    {
        $result = [];

        $from_date = Carbon::parse($data->from);
        $to_date = Carbon::parse($data->to);
        $dates = CarbonPeriod::create($from_date, $to_date);
        $days_of_week = $data->days;

        foreach ($dates as $date) {
            $day = $date->dayOfWeek;
            if(($days_of_week && in_array($day, $days_of_week)) || (!$days_of_week)){
                $result[] = $date->format('Y-m-d');
            }
        }
        return $result;
    }


}
