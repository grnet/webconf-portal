<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Libraries\BBB\BigBlueButton;
use Illuminate\Support\Facades\URL;
use App\Room;
use App\Meeting;
use App\Invitation;
use App\Participant;
use Auth;
use Exception;
use Response;

class bbbController extends Controller
{

    protected static function create($room){
	    	//create room to next available bbb
	    	do{
		    	$bbb = new BigBlueButton();
		} while(!$bbb->isUp());
		$record = 'false';
		if($room->recording == 1){
			$record = 'true';
		}

		$creationParams = array(
			'meetingId' => $room->bbb_meeting_id, // REQUIRED
			'meetingName' => $room->name, // REQUIRED
			'attendeePw' => $room->att_pass, // Match this value in getJoinMeetingURL() to join as attendee.
			'moderatorPw' => $room->mod_pass, // Match this value in getJoinMeetingURL() to join as moderator.
			'logoutUrl' => URL::to('/'),// Default in bigbluebutton.properties. Optional.
			'record' => $record,
			'dialNumber' => '',
			'voiceBridge' => '',
			'webVoice' => '',
			'maxParticipants' => '',
			'duration' => '',
			'welcomeMsg' => '',
		);

		$itsAllGood = true;
		try {
			$result = $bbb->createMeetingWithXmlResponseArray($creationParams);
		}
		catch (Exception $e) {
			$itsAllGood = false;
			throw new Exception($e->getMessage(). "\n");
		}
		if ($itsAllGood == true) {
			// If it's all good, then we've interfaced with our BBB php api OK:
			if ($result == null) {
				// If we get a null response, then we're not getting any XML back from BBB.
				return false;
			}       
			else {
				// We got an XML response, so let's see what it says:
				if ($result['returncode'] == 'SUCCESS') {
					//store meeting to db
					$meeting = new Meeting();
					$meeting->room = $room->id;
					$meeting->bbb_server = $bbb->id;
					$meeting->save();
					return $bbb->id;
				}
				else {
					return false;
				}
			}
		}
		
    }
   
    public static function running($room){
	$last_meeting = $room->meetings()->latest('created_at')->first();
	if($last_meeting){
		//check if last meeting is still running on server
		$bbb = new BigBlueButton($last_meeting->bbb->id);
		//if server is down room is not running
		if($bbb->isUp()){
			$result = $bbb->isMeetingRunningWithXmlResponseArray($room->bbb_meeting_id);
			if($result['running'] == 'true'){
				//return server id
				return $last_meeting->bbb->id;
			}
		}
		return false;
	}
    }

    public static function runningAjax($room_id){
	$room = Room::findOrFail($room_id);
        $access = roomController::checkAccess($room);
	if(!$access){
		return Response::json(array('running' => false));
	}
	$last_meeting = $room->meetings()->latest('created_at')->first();
	if($last_meeting){
		//check if last meeting is still running on server
		$bbb = new BigBlueButton($last_meeting->bbb->id);
		//if server is down room is not running
		if($bbb->isUp()){
			$result = $bbb->isMeetingRunningWithXmlResponseArray($room->bbb_meeting_id);
			if($result['running'] == 'true'){
				//return server id
				return Response::json(array('running' => true));;
			}
		}
		return Response::json(array('running' => false));;
	}
    }


    public function join_pin(Request $request){
	//validate options
	$validator = [
		'access_pin' => ['required', 'numeric'],
		'g-recaptcha-response' => 'required|captcha'
	];
	//tsipizic for unit testing no captcha
	if($request->getHost() == 'localhost'){
		$validator = [
			'access_pin' => ['required', 'numeric']
		];
	}
	$this->validate($request,$validator);
	$access_pin = $request->input('access_pin');
	$room = Room::where('access_pin', $access_pin)->first();
	if($room){
		//check if meeting running and create if needed
		$bbb_id = bbbController::running($room);
		if(!$bbb_id){
			$bbb_id = bbbController::create($room);
		}

		//join meeting
		$bbb = new BigBlueButton($bbb_id);
		$params = array(
			'meetingId' => $room->bbb_meeting_id,
			'username' => 'test',
			'userId' => '',
			'webVoiceConf' => '',
			'password' => $room->att_pass
		);
		try {
			$result = $bbb->getJoinMeetingURL($params);
		}
		catch(Exception $e){
			throw new Exception($e->getMessage(). "\n");
		}
		return redirect($result);
	}
	else{
		return view('room.withPin')->withErrors(trans('room.not_found'));
	}

    }

    //route for external participants with token
    public function join_external(Request $request,$token){
	$inv = Invitation::where('token', '=' ,$token)->firstOrFail();
	$part = Participant::findOrFail($inv->participant);
	$room = Room::findOrFail($inv->room);
	if($part->moderator){
                $pass = $room->mod_pass;
        }
        else{
                $pass = $room->att_pass;
        }
	//check if meeting running and create if needed
	$bbb_id = bbbController::running($room);
	if(!$bbb_id){
		$bbb_id = bbbController::create($room);
	}

	//join meeting
	$bbb = new BigBlueButton($bbb_id);
	$params = array(
		'meetingId' => $room->bbb_meeting_id,
		'username' => $part->mail,
		'userId' => '',
		'webVoiceConf' => '',
		'password' => $pass
	);
	try {
		$result = $bbb->getJoinMeetingURL($params);
	}
	catch(Exception $e){
		throw new Exception($e->getMessage(). "\n");
	}
	return redirect($result);

    }


    public function join($id){
	$room = Room::findOrFail($id);
	$access = roomController::checkAccess($room);
	if(!$access){
		throw new Exception('Unauthorized');
	}
	else if($access == 1){
		$pass = $room->mod_pass;
	}
	else{
		$pass = $room->att_pass;
	}

	//check if meeting running and create if needed
	$bbb_id = bbbController::running($room);
	if(!$bbb_id){
		$bbb_id = bbbController::create($room);
	}

	$user = Auth::user();

	//join meeting
	$bbb = new BigBlueButton($bbb_id);
	$params = array(
		'meetingId' => $room->bbb_meeting_id,
		'username' => $user->mail,
		'userId' => '',
		'webVoiceConf' => '',
		'password' => $pass
	);
	try {
		$result = $bbb->getJoinMeetingURL($params);
	}
	catch(Exception $e){
		throw new Exception($e->getMessage(). "\n");
	}
	return redirect($result);

    }

}
