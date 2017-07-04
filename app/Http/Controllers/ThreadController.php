<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\User;
use App\Thread;
class ThreadController extends Controller
{
    function __construct()
    {
        return $this->middleware('auth')->except('index');

    }
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $threads = Thread::paginate(15);
        return view('thread.index', compact('threads'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    //create a notification - form
    public function create()
    {
        return view('thread.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    //store notification form entries
    public function store(Request $request)
    {
        //validate

        $this->validate($request, [
            'subject' => 'required|min:5',
            'type'    => 'required',
            'thread'  => 'required|min:10',
            
        ]);

        //store
        
        Thread::create($request->all());
        $threadarray = $request->all();
        error_log(json_encode($threadarray));
        //single device token for testing
        $regId = 'all';
        app('App\Http\Controllers\FirebaseController')->send($regId, json_encode($threadarray));
        //redirect
        //return back()->withMessage('Thread Created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Thread $thread
     * @return \Illuminate\Http\Response
     */
    public function show(Thread $thread)
    {
        return view('thread.single', compact('thread'));
    }
}
