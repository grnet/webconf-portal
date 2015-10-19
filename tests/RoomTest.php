<?php


//use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\User;
use App\Room;
use \DB;

class RoomTest extends TestCase {

	//use WithoutMiddleware;

	private $user;

	/**
	 * Create user for testing
	*/
	public function setUp(){
		parent::setUp();

                $user = new user();
                $user->id = -1;
                $user->mail = 'unittesting@unittesting.com';
                $user->save();

		$this->user = $user;

		$this->be($user);
	}



	/**
	 * Creates room and joins
	*/
	public function testCreateAndJoinRoom(){
		$this->visit('/room/create')
			->type('unit_testing','name')
			->check('public')
			->press('my_submit');
		$room = Room::where('name', 'unit_testing')->firstOrFail();
		/* TODO check for successful join
			$this->visit('/room/own')->click('join_'.$room->id);
		*/
	}	


	/**
	 * Test seeing own room
	*/
	public function testSeeOwnRoom(){
		//create public to see it
		$this->visit('/room/create')
			->type('unit_testing','name')
			->check('public')
			->press('my_submit');

		$this->visit('/room/own')->see('unit_testing');
	}

	/**
	 * Test seeing public room
	*/
	public function testSeePublicRoom(){
		$this->visit('/room/public')->see('tsipizic record');
	}


	/**
	 * Edits room and add participant to check if he sees it
	 */
	public function testEditRoom(){
		//create room to edit later
		$this->visit('/room/create')
			->type('unit_testing','name')
			->check('public')
			->press('my_submit');
		//edit room
		$room = Room::where('name', 'unit_testing')->firstOrFail();
		$data = ['_method' => 'PUT', 'name'=>'unit_testing', 'recording' => 1, 'email[]' => 'participant@unittesting.com', '_token' => csrf_token()];
		$response = $this->call('PUT', '/room/update/'.$room->id, $data);
		$this->assertEquals(302, $response->getStatusCode());
		//check room is recorded so edit works
		$this->visit('/room/show/'.$room->id)->see('Conferences will be recorded');
		
		/* TODO check for participant sees the room some error with post
                $user2 = new user();
                $user2->id = -2;
                $user2->mail = 'participant@unittesting.com';
                $user2->save();

                $this->be($user2);
		$this->visit('/room/invited')->see('unit_testing');

		$user2->delete();
		*/

	}

	/**
	 * Test join with pin
	 */
	public function testJoinPin(){
                $this->visit('/room/create')
                        ->type('unit_testing','name')
                        ->check('public')
                        ->press('my_submit');
                //edit room
                $room = Room::where('name', 'unit_testing')->firstOrFail();
		$this->visit('/room/withPin')
			->type($room->access_pin, 'access_pin');
			//TODO crashes from external redirect
			//->press('join_withPin_submit');
	}	
	/**
	 * Test portal navigation
	 */
	public function testNav(){
		$this->visit('/')->click('Help')->see('Check your connection quality');
	}

	/**
	 * Cleanup
	 */
	public function tearDown(){
		DB::delete('DELETE FROM rooms WHERE owner = ?', [$this->user->id]);
		$this->user->delete();
	}
}
