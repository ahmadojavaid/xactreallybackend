<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Settings;
use App\Models\Job;
use App\Models\Elevation;
use App\Models\Room;
use App\Models\Image;
use App\Models\JobAccess;
use App\Models\JobQuote;
use App\Models\JobQuoteMedia;
use App\Models\JobQuoteLine;

class UserController extends Controller
{
    public function getProfile(){ 
        $user = auth()->user();
        $response = [
            'success' => true,
            'message' => 'User Profile Here',
            'user' => $user
        ];

        return response()->json($response);
    }

    public function updateProfile(Request $request){
        $validator = validator()->make($request->all(), [

            'email' => 'unique:users,'.auth()->user()->_id,
            'phone' => 'unique:users,'.auth()->user()->_id
        ], [
            'phone.unique' => 'The phone number belongs to an existing account'
        ]);

        if ($validator->fails()) {

            $response = [
                'success' => false,
                'message' => $validator->errors()->first()
            ];

            return response()->json($response, 403);
        }
        
        $updateArr = array();

        if(isset($request->first_name) && !empty($request->first_name))
            $updateArr['first_name'] = $request->first_name;
        
        if(isset($request->last_name) && !empty($request->last_name))
            $updateArr['last_name'] = $request->last_name;

        if(isset($request->email) && !empty($request->email))
            $updateArr['email'] = $request->email;

        if(isset($request->phone) && !empty($request->phone))
            $updateArr['phone'] = $request->phone;        

        if(isset($request->country) && !empty($request->country))
            $updateArr['country'] = $request->country;

        if(isset($request->password) && !empty($request->password))
            $updateArr['password'] = $request->password;

        if(isset($request->profile_pic) && !empty($request->profile_pic))
            $updateArr['profile_pic'] = $request->profile_pic;

        $user =  auth()->user()->update($updateArr);

        $response = [
            'success' => true,
            'message' => 'Update Profile successfully',
            'user' => auth()->user()
        ];

        return response()->json($response);
    }

    public function getUserJobs()
    {
    	$user_id = auth()->user()->_id;
    	$myJobs = Job::where('user_id', $user_id)->get();

    	$assignedJobs = Job::with('jobAccess')->whereHas('jobAccess',function($q) use ($user_id){
        	$q->where(['user_id' => $user_id , 'accept_invitation' => 'accept']);
        })->get();

        //Unset job_access
        $assignedJobs = $assignedJobs->map( function($assignedJob){
            unset($assignedJob->jobAccess);
            return $assignedJob;
        });
        $response = [
            'success' => true,
            'jobs' => $myJobs,
            'assigned_jobs' => $assignedJobs
        ];

        return response()->json($response);
    }

    public function searchUsers(Request $request)
    {
    	$validator = validator()->make($request->all(), [

            'name' => 'required'
        ], [
            'name' => 'Must Send Name'
        ]);

        if ($validator->fails()) {

            $response = [
                'success' => false,
                'message' => $validator->errors()->first()
            ];

            return response()->json($response, 403);
        }

    	$users = User::where('first_name','like',$request->name."%")->get();
        $response = [
            'success' => true,
            'users' => $users
        ];


        return response()->json($response);
    }

    public function getUserJobQuotes()
    {
    	$user_id = auth()->user()->_id;
    	$jobQuotes = JobQuote::where('user_id', $user_id)->get();

        $response = [
            'success' => true,
            'job_quotes' => $jobQuotes,
        ];

        return response()->json($response);
    }

    public function getUserSentJobInvitations()
    {
        $user_id = auth()->user()->_id;
        $sentJobInvitations = JobAccess::with('job','user')
        ->whereHas('job',function($query) use ($user_id){
                $query->where('user_id' , $user_id);
            })
        ->get();

        //Unset job Key from Collection
        $sentJobInvitations = $sentJobInvitations->map(function($sentJobInvitation){
            unset($sentJobInvitation->job);
            return $sentJobInvitation;
        });
        $response = [
            'success' => true,
            'sent_job_invitations' => $sentJobInvitations,
        ];

        return response()->json($response);
    }

    public function getUserReceivedJobInvitations()
    {
        $user_id = auth()->user()->_id;
        $receivedJobInvitations = JobAccess::with('user')
        ->where('user_id', $user_id)
        ->get();

        $response = [
            'success' => true,
            'received_job_invitations' => $receivedJobInvitations,
        ];

        return response()->json($response);
    }
}
