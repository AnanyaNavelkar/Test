<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\User;
use App\Thread;
use DB;
class FirebaseController extends Controller
{
    function __construct()
    {
        return $this->middleware('auth')->except('index');
    }

    /*public function inserttoken($dt, $email)
    {
        error_log("in insert token");
        $query = DB::table('users')->where('email',$email)->get();
        $id = $query->id;
        //$user = User::find($id);
        $result = DB::table('users')
            ->where('id', $id)
            ->update(['devicetoken' => $dt]);
        return $result;
    }*/

    // sending push message to single user by firebase reg id
    public function send($to, $message) {
        error_log("in firebase controller");
    $fields = array(
    'to' => 'e2bnygsMLq4:APA91bEVu-tAV83BOyH0w5k015SjOU4hAgV0OdBAodgbygSFIHQkP6VhZJ9eU6WovZ_0VO5y4fMmvfH2rCkV__f7gQa2a8K7Wyq47R6TVu43S5VDrPzL2fHlgCu9MDjBrcP4JMp1zMtg',
       // 'to' => '/topics/'.$to,
        'data' => array('message' => $message),
    );
    return $this->sendPushNotification($fields);
    }
    // function makes curl request to firebase servers
    private function sendPushNotification($fields) {
         error_log("in send push notifs");
        //require_once __DIR__ . '/config.php';
 
        // Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send';
 
        $headers = array(
            //'Authorization: key=' . FIREBASE_API_KEY,
            'Authorization: key=AAAAYHVtmwc:APA91bERG6hKbZ1w2zolcVaBM_2NSauLycFFTjweqGQJkM9sBMNa7A_o7NTodp_tze_FW3s4bxUK2jNIA4iQjtqxFKphqMuOo36RqlS5kCwgGwXfFQu_dj8xmdz9R7bRDpvEZtZC3c9T',
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();
        echo "connection opened";
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($ch, CURLOPT_IPRESOLVE, CURLOPT_IPRESOLVE_V4);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        echo "settings done";
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        echo "curl executed";
        // Close connection
        curl_close($ch);

        dd($result);

 
        return $result;
    }
    
}
