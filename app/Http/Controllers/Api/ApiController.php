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
use App\Models\Access;
use App\Models\JobAccess;
use App\Models\JobQuote;
use App\Models\JobQuoteMedia;
use App\Models\JobQuoteLine;

class ApiController extends Controller
{
    /* *************************** Auth API's ********************************************************** */
        public function signUp(Request $request)
        {   
            $validator = validator()->make($request->all(), [

                'email' => 'unique:users',
                'phone' => 'unique:users'
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

            if (!$request->phone_verified) {

                $response = [
                    'success' => true,
                    'message' => 'Please verify phone to continue'
                ];

                return response()->json($response);
            }

            $user = new User();
            $user->fill($request->all());
            $user->generateAuthToken();

            $response = [
                'success' => true,
                'message' => 'Signed up successfully',
                'user' => $user
            ];

            return response()->json($response);
        }

        public function signIn(Request $request)
        {
            if (auth()->attempt($request->only('email', 'password'))) {
                
                $user = auth()->user();

                if (!$user->auth_token) {
                    $user->generateAuthToken();
                }

                $response = [
                    'success' => true,
                    'message' => 'Signed in successfully',
                    'user' => $user
                ];

                return response()->json($response);
            }

            $response = [
                'success' => false,
                'message' => 'Incorrect email or password'
            ];

            return response()->json($response, 401);
        }

        public function checkPhone(Request $request)
        {
            $user = User::where('phone', $request->phone)->first();
            if ($user) {

                $response = [
                    'success' => true,
                    'message' => 'Phone number exists in the records',
                    'user' => $user
                ];

                return response()->json($response);
            }

            $response = [
                'success' => false,
                'message' => 'Phone number doesn\'t exist in the records'
            ];

            return response()->json($response, 404);
        }

        public function changePassword(Request $request)
        {
            auth()->user()->update(['password' => $request->password]);

            $response = [
                'success' => true,
                'message' => 'Password changed successfully'
            ];

            return response()->json($response);
        }

        public function chooseRole(Request $request)
        {
            $user = auth()->user();
            $user->update(['role' => $request->role]);

            $response = [
                'success' => true,
                'message' => 'Role set successfully',
                'user' => $user
            ];

            return response()->json($response);
        }
    /* *************************** Auth API's ********************************************************** */

    /* *************************** Static Page API's *************************************************** */
        public function privacyPolicy()
        {
            $response = [
                'success' => true,
                'privacy_policy' => Settings::where('key', 'privacy_policy')->first()->value
            ];

            return response()->json($response);
        }

        public function termsConditions()
        {
            $response = [
                'success' => true,
                'terms_conditions' => Settings::where('key', 'terms_conditions')->first()->value
            ];

            return response()->json($response);
        }
    /* *************************** Static Page API's *************************************************** */

    /* *************************** Job & Room & Elevation API's **************************************** */
        public function jobs()
        {
            $response = [
                'success' => true,
                'jobs' => Job::where('published', "1")->get()
            ];

            return response()->json($response);
        }

        public function job($id)
        {
            $response = [
                'success' => true,
                'job' => Job::with(['elevations.images', 'rooms.images'])
                    ->where('_id', $id)
                    ->first()
            ];

            return response()->json($response);
        }

        public function createJob(Request $request)
        {
            $job = new Job();
            $job->fill($request->all());
            $job->user_id = auth()->user()->_id;
            $job->save();

            $response = [
                'success' => true,
                'message' => 'Job created successfully',
                'job' => $job
            ];

            return response()->json($response);
        }

        public function updateJob($id, Request $request)
        {
            $job = Job::find($id);
            $job->fill($request->all());
            $job->save();

            $response = [
                'success' => true,
                'message' => 'Job updated successfully',
                'job' => $job
            ];

            return response()->json($response);
        }

        public function deleteJob($id)
        {
            Job::destroy($id);

            $response = [
                'success' => true,
                'message' => 'Job deleted successfully'
            ];

            return response()->json($response);
        }

        public function addElevation(Request $request)
        {
            $elevation = new Elevation();
            $elevation->fill($request->all());
            $elevation->save();

            if ($request->images) {
                Image::upload('elevation', $request->images, $elevation->id);
            }

            $response = [
                'success' => true,
                'message' => 'Elevation added successfully',
                'elevation' => Elevation::with('images')
                    ->where('_id', $elevation->id)
                    ->first()
            ];

            return response()->json($response);
        }

        public function updateElevation($id, Request $request)
        {
            $elevation = Elevation::find($id);
            $elevation->fill($request->all());
            $elevation->save();

            if ($request->new_images) {
                Image::upload('elevation', $request->new_images, $elevation->id);
            }

            if ($request->deleted_image_ids) {
                Image::remove($request->deleted_image_ids);
            }

            $response = [
                'success' => true,
                'message' => 'Elevation updated successfully',
                'elevation' => Elevation::with('images')
                    ->where('_id', $elevation->id)
                    ->first()
            ];

            return response()->json($response);
        }

        public function deleteElevation($id)
        {
            $elevation = Elevation::find($id);
            $elevation->images()->delete();
            $elevation->delete();

            $response = [
                'success' => true,
                'message' => 'Elevation deleted successfully'
            ];

            return response()->json($response);
        }

        public function getElevationbyId($id)
        {
            $elevation = Elevation::where('_id' , $id)->with('images')->first();

            $response = [
                'success' => true,
                'elevation' => $elevation
            ];

            return response()->json($response);
        }

        public function addRoom(Request $request)
        {
            $room = new Room();
            $room->fill($request->all());
            $room->save();

            if ($request->images) {
                Image::upload('room', $request->images, $room->id);
            }

            $response = [
                'success' => true,
                'message' => 'Room added successfully',
                'room' => Room::with('images')
                    ->where('_id', $room->id)
                    ->first()
            ];

            return response()->json($response);
        }

        public function updateRoom($id, Request $request)
        {
            $room = Room::find($id);
            $room->fill($request->all());
            $room->save();

            if ($request->new_images) {
                Image::upload('room', $request->new_images, $room->id);
            }

            if ($request->deleted_image_ids) {
                Image::remove($request->deleted_image_ids);
            }

            $response = [
                'success' => true,
                'message' => 'Room updated successfully',
                'room' => Room::with('images')
                    ->where('_id', $room->id)
                    ->first()
            ];

            return response()->json($response);
        }

        public function deleteRoom($id)
        {
            $room = Room::find($id);
            $room->images()->delete();
            $room->delete();

            $response = [
                'success' => true,
                'message' => 'Room deleted successfully'
            ];

            return response()->json($response);
        }

        public function getRoombyId($id)
        {
            $room = Room::where('_id' , $id)->with('images')->first();

            $response = [
                'success' => true,
                'room' => $room
            ];

            return response()->json($response);
        }
    /* *************************** Job & Room & Elevation API's **************************************** */

    /* *************************** Job Access API's **************************************************** */
        public function getAccess()
        { 
            $access = Access::where('status' , 'yes')->get();
            $response = [
                'success' => true,
                'message' => 'Access list Here',
                'access' => $access
            ];

            return response()->json($response);
        }

        public function addJobAccess(Request $request)
        {

            $jobAccess = JobAccess::updateOrCreate([
                'job_id' => $request->job_id,
                'user_id' => $request->user_id,
                'access_id' => $request->access_id,
                'accept_invitation' => 'pending' 
            ]);
            $response = [
                'success' => true,
                'message' => 'Job Access Added successfully',
            ];

            return response()->json($response);
        }

        public function acceptJobInvitation($job_access_id)
        {
            $jobAccess = JobAccess::where('_id' , $job_access_id)
            ->update(['accept_invitation' => 'accept']);
            
            $response = [
                'success' => true,
                'message' => 'Job Invitation Accept Successfully',
            ];

            return response()->json($response);
        }

        public function cancelJobInvitation($job_access_id)
        {
            $jobAccess = JobAccess::where('_id' , $job_access_id)
            ->update(['accept_invitation' => 'cancel']);

            $response = [
                'success' => true,
                'message' => 'Job Invitation Cancel Successfully',
            ];

            return response()->json($response);
        }

        public function deleteJobInvitation($job_access_id)
        {
            $jobAccess = JobAccess::where('_id' , $job_access_id)
            ->delete();

            $response = [
                'success' => true,
                'message' => 'Job Invitation Delete Successfully',
            ];

            return response()->json($response);
        }
    /* *************************** Job Access API's **************************************************** */

    /* *************************** Job Quote API's ***************************************************** */
        public function addJobQuote(Request $request)
        {
            $validator = validator()->make($request->all(), [

                'name' => 'required',
                'description' => 'required',
                'quantity' => 'required',
                'tax' => 'required',
                'recoverable_cost' => 'required',
                'o_and_p' => 'required',
                'total' => 'required',
                'status_remove_replace' => 'required',
                'job_id' => 'required',
                'documents.*' => 'mimes:doc,pdf,docx,zip,xls,xlsx,ppt|max:5200',
            ], [
                'name' => 'Name Field Required',
                'description' => 'Description Field Required',
                'quantity' => 'Quantity Field Required',
                'tax' => 'Tax Field Required',
                'recoverable_cost' => 'Recoverable Cost Field Required',
                'o_and_p' => 'O&P Field Required',
                'total' => 'Total Field Required',
                'status_remove_replace' => 'Select Remove & Replace Type',
                'job_id' => 'Job Id Required',
            ]);

            if ($validator->fails()) {

                $response = [
                    'success' => false,
                    'message' => $validator->errors()->first()
                ];

                return response()->json($response, 403);
            }
            $user_id = auth()->user()->_id;

            $jobQuote = JobQuote::create([
                'name' => $request->name,
                'description' => $request->description,
                'quantity' => $request->quantity,
                'tax' => $request->tax,
                'recoverable_cost' => $request->recoverable_cost,
                'o_and_p' => $request->o_and_p,
                'total' => $request->total,
                'status_remove_replace' => $request->status_remove_replace,
                'image_url' => getDefaultImageUrl('job_quote'),
                'user_id' => $user_id,
                'job_id' => $request->job_id,
            ]);
            if($request->hasFile('documents')){
                $job_quote_id = $jobQuote->_id;
                $model_type = 'JobQuote';
                $media_type = 'document';
                foreach ($request->documents as $key => $document) {
                    $jobQuoteMedia_m = new JobQuoteMedia();
                    $name = 'documents/'.$job_quote_id.'-'.time().'-'. $key;
                    $name = $jobQuoteMedia_m->uploadMedia($model_type, $job_quote_id, $document, $name);

                    $jobQuoteMedia = JobQuoteMedia::create([
                        'media_name' => $name,
                        'media_type' => $media_type,
                        'media_url' => $name,
                        'model_type' => $model_type,
                        'model_id' => $job_quote_id
                    ]);
                }
            }
            $response = [
                'success' => true,
                'message' => 'Job Quote Added successfully',
                'job_quote' => $jobQuote
            ];
            return $response;
        }

        public function updateJobQuote($jobQuoteId , Request $request)
        {
            $validator = validator()->make($request->all(), [

                'name' => 'required',
                'description' => 'required',
                'quantity' => 'required',
                'tax' => 'required',
                'recoverable_cost' => 'required',
                'o_and_p' => 'required',
                'total' => 'required',
                'status_remove_replace' => 'required',
                'documents.*' => 'mimes:doc,pdf,docx,zip,xls,xlsx,ppt|max:5200',
            ], [
                'name' => 'Name Field Required',
                'description' => 'Description Field Required',
                'quantity' => 'Quantity Field Required',
                'tax' => 'Tax Field Required',
                'recoverable_cost' => 'Recoverable Cost Field Required',
                'o_and_p' => 'O&P Field Required',
                'total' => 'Total Field Required',
                'status_remove_replace' => 'Select Remove & Replace Type',
            ]);

            if ($validator->fails()) {

                $response = [
                    'success' => false,
                    'message' => $validator->errors()->first()
                ];

                return response()->json($response, 403);
            }
            $user_id = auth()->user()->_id;

            $jobQuote = JobQuote::where('_id' , $jobQuoteId )->update([
                'name' => $request->name,
                'description' => $request->description,
                'quantity' => $request->quantity,
                'tax' => $request->tax,
                'recoverable_cost' => $request->recoverable_cost,
                'o_and_p' => $request->o_and_p,
                'total' => $request->total,
                'status_remove_replace' => $request->status_remove_replace,
                'image_url' => getDefaultImageUrl('job_quote'),
                'user_id' => $user_id,
            ]);

            //deleted document of job quote
            if($request->remove_document_id){
                $ids = explode(',', $request->remove_document_id);
                JobQuoteMedia::whereIn('_id' , $ids)->delete();
            }
            if($request->hasFile('documents')){
                $job_quote_id = $jobQuoteId;
                $model_type = 'JobQuote';
                $media_type = 'document';
                foreach ($request->documents as $key => $document) {
                    $jobQuoteMedia_m = new JobQuoteMedia();
                    $name = 'documents/'.$job_quote_id.'-'.time().'-'. $key;
                    $name = $jobQuoteMedia_m->uploadMedia($model_type, $job_quote_id, $document, $name);

                    $jobQuoteMedia = JobQuoteMedia::create([
                        'media_name' => $name,
                        'media_type' => $media_type,
                        'media_url' => $name,
                        'model_type' => $model_type,
                        'model_id' => $job_quote_id
                    ]);
                }
            }
            $response = [
                'success' => true,
                'message' => 'Job Quote Update successfully',
                'job_quote' => JobQuote::find($jobQuoteId)
            ];
            return $response;
        }

        public function getJobQuoteLines($job_quote_id)
        { 
            $job_quote_lines = JobQuoteLine::where('job_quote_id' , $job_quote_id)->get();
            $response = [
                'success' => true,
                'message' => 'Job Quote Lines list Here',
                'job_quote_lines' => $job_quote_lines
            ];

            return response()->json($response);
        }

        public function addJobQuoteLine(Request $request)
        {
            $validator = validator()->make($request->all(), [

                'price' => 'required',
                'quantity' => 'required',
                'description' => 'required',
                'job_quote_id' => 'required',
                'images.*' => 'mimes:jpg,jpeg,png|max:5200',
            ], [
                'price' => 'Name Field Required',
                'quantity' => 'Quantity Field Required',
                'description' => 'Description Field Required',
                'job_quote_id' => 'Job Id Required',
            ]);

            if ($validator->fails()) {

                $response = [
                    'success' => false,
                    'message' => $validator->errors()->first()
                ];

                return response()->json($response, 403);
            }

            $jobQuoteLine = JobQuoteLine::create([
                'price' => $request->price,
                'quantity' => $request->quantity,
                'description' => $request->description,
                'image_url' => getDefaultImageUrl('job_quote_line'),
                'job_quote_id' => $request->job_quote_id,
            ]);
            if($request->hasFile('images')){
                $job_quote_line_id = $jobQuoteLine->_id;
                $model_type = 'JobQuoteLine';
                $media_type = 'image';
                foreach ($request->images as $key => $image) {
                    $jobQuoteMedia_m = new JobQuoteMedia();
                    $name = 'images/'.$job_quote_line_id.'-'.time().'-'. $key;
                    $name = $jobQuoteMedia_m->uploadMedia($model_type, $job_quote_line_id, $image, $name);

                    $jobQuoteMedia = JobQuoteMedia::create([
                        'media_name' => $name,
                        'media_type' => $media_type,
                        'media_url' => $name,
                        'model_type' => $model_type,
                        'model_id' => $job_quote_line_id
                    ]);
                }
            }
            $response = [
                'success' => true,
                'message' => 'Job Quote Line Added successfully',
                'job_quote_line' => $jobQuoteLine
            ];
            return $response;
        }

        public function getJobQuoteById($job_quote_id)
        { 
            $job_quote = JobQuote::where('_id' , $job_quote_id)->with('JobQuoteMedia')->first();
            $response = [
                'success' => true,
                'message' => 'Job Quote With Detail',
                'job_quote' => $job_quote
            ];

            return response()->json($response);
        }

        public function getJobQuoteLineById($job_quote_line_id)
        { 
            $job_quote_line = JobQuoteLine::where('_id' , $job_quote_line_id)->with('JobQuoteMedia')->first();
            $response = [
                'success' => true,
                'message' => 'Job Quote Lines list Here',
                'job_quote_line' => $job_quote_line
            ];

            return response()->json($response);
        }

        public function deleteJobQuoteLineById($job_quote_line_id)
        { 
            $job_quote_line = JobQuoteLine::find($job_quote_line_id);
            $job_quote_line->JobQuoteMedia()->delete();
            $job_quote_line->delete();
            $response = [
                'success' => true,
                'message' => 'Job Quote Line deleted successfully',
            ];

            return response()->json($response);
        }
    /* *************************** Job Quote API's ***************************************************** */
}