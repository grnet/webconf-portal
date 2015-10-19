<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Illuminate\Validation\Validator;
use Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Libraries\BBB\BigBlueButton;
use App\Room;
use App\Participant;
use Auth;
use Redirect;
use Exception;
use Mail;
use Illuminate\Support\Str;
use App\Invitation;

class roomController extends Controller
{
    private static function isAcademicEmail($mail){
	//TODO add academic domains
	$acdomains = array('auth.gr');
	
	$email = explode('@',$mail);
	$domain = $email[1];
	if(Str::endsWith($domain, $acdomains)){
		return true;
	}
	return false;
    }

    //returns 1 for moderator, 2 for participant and false for no access	
    public static function checkAccess($room){
    	$user = Auth::user();
	if($room->owner == $user->id){
		return 1;
	}
	foreach($room->participants as $part){
		if($part->mail == $user->mail){
			if($part->moderator){
				return 1;
			}
			else{
				return 2;
			}
		}
	}
	if($room->public == 1){
		return 2;
	}

	return false;

    }

    public static function checkOwner($room){
	$user = Auth::user();
	if($room->owner == $user->id){
		return true;
	}
	return false;
    }

    private static function createInvitationToken($room,$part){
	$inv = new Invitation();
	$inv->room = $room->id;
	$inv->participant = $part->id;
	\DB::transaction(function () use ($inv) {
		//check for unique token before save
		do{
			$token = Str::random(32);
		}while(Invitation::where('token' , '=' , $token)->count() != 0);
		$inv->token = $token;
		$inv->save();
	});
	return $inv->token;
    }

    public function invite(Request $request, $id){
        $room = Room::findOrFail($id);
	if(!roomController::checkOwner($room)){
		throw new Exception('Unauthorized');
	}
	$user = Auth::user();
	//make validations
        $validator = Validator::make($request->all(), [
            'date' => 'required'
        ]);
	$text = $request->input('invite_text');
	$date = $request->input('date');
	
	//check if no participants
	if($room->participants->count() == 0){
		return Redirect::action('roomController@show',$id)->with('message', trans('room.show.invite_no_participants'));
	}
	//send invites
	foreach($room->participants as $part){
		if(!$this->isAcademicEmail($part->mail)){
			$token = $this->createInvitationToken($room,$part);
			Mail::send('emails.invite_external', ['text' => $text,'date' => $date,'token' => $token,'owner_mail' => $user->mail], function($message) use ($part,$user)
			{
				    $message->to($part->mail)->subject('Webconf Invitation');
			});

		}
		else{
			//else just send email
			Mail::send('emails.invite', ['text' => $text,'date' => $date, 'room_name' => $room->name, 'owner_mail' => $user->mail ], function($message) use ($part,$user)
			{
				    $message->to($part->mail)->subject('Webconf Invitation');
			});
		}
	}
	//send mail to owner
	Mail::send('emails.invite_owner', ['text' => $text,'date' => $date, 'room_name' => $room->name, 'parts' => $room->participants], function($message) use ($part,$user)
	{
		    $message->to($user->mail )->subject('Webconf Invitation');
	});

	return Redirect::action('roomController@show',$id)->with('message', trans('room.show.invite_success'));
    }



    public function own(){
        $user = Auth::user();
	$rooms = $user->rooms;
	return view('room.rooms', ['rooms' => $rooms]);
    }

    public function invited(){
	$user = Auth::user();
	$participations = Participant::where('mail', $user->mail)->get();
	$rooms = array();
	foreach($participations as $part){
		$rooms[] = $part->room;
	}
	return view('room.rooms', ['rooms' => $rooms]);

    }

    public function publicr(){
	$rooms = Room::where('public','1')->get();
	return view('room.rooms', ['rooms' => $rooms]);

    }

    public function withPin(){
	return view('room.withPin');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request)
    {

	//mpainoume se auth thn diadikasia giati eite 8a kanoume populate thn forma thn prwth fora me
        //tous participants apo thn bash, eite se periptwsh la8os validation prepei na gemisoume thn
        //forma pali, alla oxi me tous participants ths bashs , alla me ta dedomena tou xrhsth tou
        //prohgoumenou POST ths formas me ta errors!
        $email_moderators = array();
        if(session('validation_err')){
                $emails = $request->old('email', array());
                $mods = $request->old('moderator', array());

                foreach($emails as $em){
                        if(in_array($em, $mods))
                          $email_moderators[] = array($em, 1);
                        else
                          $email_moderators[] = array($em, 0);
                }
        }

        //return view('room.create');
	return view('room.edit', ['emails' => $email_moderators]); //den xreiazomaste 2 formes, mono mia
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
	//make validations
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'array'
        ]);

        $validator->each('email', ['email']);

        if ($validator->fails()) {
            return redirect('room/create')
                        ->withErrors($validator)
                        ->withInput()->with('validation_err' ,  true);
        }


     	$room = new Room();
	$room->name = $request->input('name');
	$room->recording = empty($request->input('recording'))? 0 : 1;
	$room->public = empty($request->input('public'))? 0 : 1;
	$room->att_pass = Str::quickRandom(8);
	$room->mod_pass = Str::quickRandom(8);
	//add owner
	$user = Auth::User();
	$room->owner = $user->id;

	//check if meeting id exists
	\DB::transaction(function () use ($room) {
		//check for unique access_pin and meeting id
		do{
			$bbb_meeting_id = Str::quickRandom(8);
		}while(Room::where('bbb_meeting_id' , '=' , $bbb_meeting_id)->count() != 0);
		do{
			$access_pin = mt_rand(0,999999);
		}while(Room::where('access_pin' , '=' , $access_pin)->count() != 0);
		$room->bbb_meeting_id = $bbb_meeting_id;
		$room->access_pin = $access_pin;

		$room->save();
	});

	//extract emails
	/*$mails = preg_split('/\r\n|\n|\r/', $request->input('participants'));
	$part_mails = array();
	foreach($mails as $mail){
		$part_mail = filter_var($mail, FILTER_VALIDATE_EMAIL);
		if($part_mail){
			$participant = new Participant();
			$participant->mail = $part_mail;
			$participant->room_id = $room->id;
			$participant->save();
		}
	}*/

	$emails = $request->input('email');
        $moderators = $request->input('moderator');
        if(!$moderators){
                $moderators = array();
        }
	if(is_array($emails)){
                foreach($emails as $email){
                        $participant = new Participant();
                        $participant->mail = $email;
                        $participant->room_id = $room->id;
                        if(in_array($email,$moderators)){
                                $participant->moderator = 1;
                        }
                        $participant->save();
                }
        }

	return Redirect::action('roomController@own');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $room = Room::findOrFail($id);
        if(!roomController::checkAccess($room)){
		throw new Exception('Unauthorized');
	}
	$participants = $room->participants()->get();
	$recordings = recordingsController::get($room);
	return view('room.show', ['room' => $room,'participants' => $participants, 'recordings' => $recordings, 'owner' => $room->belongs, 'check_owner' => roomController::checkOwner($room) ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(Request $request, $id)
    {
	$room = Room::findOrFail($id);
	if(!roomController::checkOwner($room)){
		throw new Exception('Unauthorized');
	}

	//mpainoume se auth thn diadikasia giati eite 8a kanoume populate thn forma thn prwth fora me
	//tous participants apo thn bash, eite se periptwsh la8os validation prepei na gemisoume thn
	//forma pali, alla oxi me tous participants ths bashs , alla me ta dedomena tou xrhsth tou
	//prohgoumenou POST ths formas me ta errors!
	$email_moderators = array();
	if(session('validation_err')){
		$emails = $request->old('email', array());
		$mods = $request->old('moderator', array());

		foreach($emails as $em){
			if(in_array($em, $mods))
			  $email_moderators[] = array($em, 1);
			else
			  $email_moderators[] = array($em, 0);
		}
	}
	else{
		$participants = $room->participants()->get();
		foreach($participants as $parts){
			$email_moderators[] = array($parts->mail, $parts->moderator);
		}
	}

	return view('room.edit', ['room' => $room, 'emails' => $email_moderators]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request,$id)
    {
	//TODO high security mode
        $room = Room::findOrFail($id);
	if(!roomController::checkOwner($room)){
		throw new Exception('Unauthorized');
	}

	//make validations
	$validator = Validator::make($request->all(), [
	    'name' => 'required',
            'email' => 'array'
        ]);

	$validator->each('email', ['email']);

        if ($validator->fails()) {
            return redirect('room/edit/'.$id)
                        ->withErrors($validator)
                        ->withInput()->with('validation_err' ,  true);
        }

	$room->name = $request->input('name');
	$room->recording = empty($request->input('recording'))? 0 : 1;
	$room->public = empty($request->input('public'))? 0 : 1;
	$room->save();

	//we save the participants
	$emails = $request->input('email');
	$moderators = $request->input('moderator');
	if(!$moderators){
		$moderators = array();
	}
	//delete old participants before adding new
	$room->participants()->delete();
	if(is_array($emails)){
		foreach($emails as $email){
                        $participant = new Participant();
                        $participant->mail = $email;
                        $participant->room_id = $room->id;
			if(in_array($email,$moderators)){
				$participant->moderator = 1;
			}
                        $participant->save();	
		}
	}

	return Redirect::action('roomController@show',$room->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
	$room = Room::findOrFail($id);
	if(!roomController::checkOwner($room)){
		throw new Exception('Unauthorized');
	}
	//get all servers meeting existed and destroy
	$bbb_servers = $room->meetings()->groupBy('bbb_server')->get(['bbb_server']);
	$recordings = array();
	//end meeting in bbb if running
	$bbb_id = bbbController::running($room);
	if($bbb_id){
		$bbb = new BigBlueButton($bbb_id);
		if($bbb->isUp()){
			$endParams = array(
				'meetingId' => $room->bbb_meeting_id, // REQUIRED - We have to know which meeting to end.
				'password' => $room->mod_pass,        // REQUIRED - Must match moderator pass for meeting.
			);
			$bbb->endMeetingWithXmlResponseArray($endParams);
		}
	}
	//delete recordings
	$recordings = recordingsController::get($room);
	if($recordings){
		//keep recordings from each server
		foreach($recordings as $rec){
			$rids[$rec->server_id] = $rec['recordId'].',';
		}
		//send requests to servers
		foreach($rids as $server_id => $recordind_ids){
			$bbb = new BigBlueButton($server_id);
			if($bbb->isUp()){
				$bbb->deleteRecordingsWithXmlResponseArray(array('recordId' => $recording_ids));
			}
		}
	}
	//delete room from database
	$room->delete();
	return Redirect::action('roomController@own');
    }
}
