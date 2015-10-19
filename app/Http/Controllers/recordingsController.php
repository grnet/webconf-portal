<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Libraries\BBB\BigBlueButton;
use App\Recording;
use App\Room;
use Auth;
use Response;
use Exception;
use Validator;
use Mail;

class recordingsController extends Controller
{
   public static function get($room){
	//get all servers meeting existed and check for recordings
	$bbb_servers = $room->meetings()->groupBy('bbb_server')->get(['bbb_server']);
	$recordings = array();
	foreach($bbb_servers as $bbb_server){
		$bbb = new BigBlueButton($bbb_server->bbb_server);
		//if server is not up do not bring recordings and continue to next server
		if(!$bbb->isUp()){
			continue;
		}
		$recordingsParams = array(
			'meetingId' => $room->bbb_meeting_id // OPTIONAL - comma separate if multiple
		);
		$result = $bbb->getRecordingsWithXmlResponseArray($recordingsParams);
		$isOwner = roomController::checkOwner($room);
		foreach($result as $recording){
			if($recording['playbackFormatUrl'] != null){
				$duration_sec = $recording['endTime'] - $recording['startTime'];
				//create download url
				$parse_url = parse_url($recording['playbackFormatUrl']);
				$download_url = 'http://'.$parse_url['host'].'/playback/presentation/download/'.$recording['recordId'].'.zip';
				$duration = gmdate("H:i:s", substr($duration_sec,0,-3));
				//find if we know recording
				$rec_query = Recording::where('rid',$recording['recordId'])->first();
				//if we dont just save
				if(!$rec_query){
					$rec_ins = new Recording();
					$rec_ins->rid = $recording['recordId'];
					$rec_ins->published = 0;
					$rec_ins->keep = 0;
					$rec_ins->bbb_server_id = $bbb->id;
					$rec_ins->owner = $room->owner;
					$rec_ins->save();
					//new recording show only to owner
					if($isOwner){
						$recordings[] = array('id' => $recording['recordId'], 'url' => $recording['playbackFormatUrl'],'time' => date('d/m/Y H:i',substr($recording['startTime'],0 ,-3)), 'duration' => $duration,'time_real' => $recording['startTime'],'download_url' => $download_url,'server_id' => $bbb->id,'keep' => false, 'portal_id' => $rec_ins->id, 'published' => false);
					}
				}
				else{
					if($rec_query->published || $isOwner){
						$recordings[] = array('id' => $recording['recordId'], 'url' => $recording['playbackFormatUrl'],'time' => date('d/m/Y H:i',substr($recording['startTime'],0 ,-3)), 'duration' => $duration,'time_real' => $recording['startTime'],'download_url' => $download_url,'server_id' => $bbb->id,'keep' => $rec_query->keep, 'portal_id' => $rec_query->id, 'published' => $rec_query->published);
					}
				}
			}
		}
	}
	if(!empty($recordings)){
        	usort($recordings,function($a, $b) { return ($b['time_real'] - $a['time_real']); });
                return $recordings;
        }
        else{
                return null;
        }

   }

   public function keep(Request $request,$rid){
	$user = Auth::user();
	$keep = 0;
	if($request->keep == "true"){
		$keep = 1;
	}
	try{
		$recording = Recording::findOrFail($rid);
		if($recording->owner != $user->id){
			return Response::json(array('status' => 'unauthorized'));
		}
		$recording->keep = $keep;
		$recording->save();
	}
	catch (Exception $ex){
		return Response::json(array('status' => $ex->getMessage()));
	}
	return Response::json(array('status' => 'success'));
    }

   public function publish(Request $request,$rid){
	$user = Auth::user();
	$publish = 0;
	if($request->publish == "true"){
		$publish = 1;
	}
	try{
		$recording = Recording::findOrFail($rid);
		if($recording->owner != $user->id){
			return Response::json(array('status' => 'unauthorized'));
		}
		$recording->published = $publish;
		$recording->save();
	}
	catch (Exception $ex){
		return Response::json(array('status' => $ex->getMessage()));
	}
	return Response::json(array('status' => 'success'));
    }

    public function share(Request $request){
        $room = Room::findOrFail($request->room_id);
	if(!roomController::checkOwner($room)){
	          throw new Exception('Unauthorized');
	}
	$bbb = new BigBlueButton($request->server_id);
	$recUrl = substr($bbb->getUrl(),0, -14).'playback/presentation/0.9.0/playback.html?meetingId='.$request->rec_id;
	$recordings = self::get($room);
	foreach($recordings as $rec){
		if($rec['id'] == $request->rec_id){
			$time = $rec['time'];
		}
	}
	foreach($room->participants as $part){
		Mail::send('emails.share_rec', ['recUrl' => $recUrl,'recTime' => $time, 'owner_mail' => $user->mail ], function($message) use ($request,$part)
		{
		    $message->to($part->mail)->subject('Recording Share');
		});
	}
	return redirect()->back()->with('message', trans('room.show.recording.sent'));
    }


    public function delete(Request $request,$rid){
	//csrf protection keep from unauthorized delete
	//no need to check for server up. User will not see recording in the first place
        $user = Auth::user();
	try{
		$recording = Recording::findOrFail($rid);
		if($recording->owner != $user->id){
			return redirect()->back()->with('message', trans('room.show.recording.delete.unauthorized'));
		}
		$bbb = new BigBlueButton($recording->bbb_server_id);
		$bbb->deleteRecordingsWithXmlResponseArray(array('recordId' => $recording->rid));
	}
	catch (Exception $ex){
		return Response::json(array('status' => $ex->getMessage()));
	}
	return redirect()->back()->with('message', trans('room.show.recording.deleted'));
    }

}
