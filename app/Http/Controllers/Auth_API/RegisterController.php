<?php

namespace App\Http\Controllers\Auth_API;

use App\Models\User;
use App\Models\Role;
use App\Mail\SendEmail;
use App\Jobs\SendEmailJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\QueryException;
// use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Validator;


class RegisterController extends Controller
{
    public function register(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:6|confirmed', 
            ]);



            if ($validator->fails()) {
                $response_code = 400;
                $response = [
                    'status' => '0',
                    'message' => $validator->errors()->toArray()
                ];
            } else {
                $user = User::create([
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'password' => Hash::make($request->input('password')),
                ]);
                
                $teacherRole = Role::where('name', 'teacher')->first(); // Assuming 'teacher' is the role name
                if ($teacherRole) {
                    $user->roles()->attach($teacherRole->id);
                }
                $response['user'] = $user;
                $response_code = 200; // Default response code
                $response = [
                    'status' => '1',
                    'message' => 'User created.'
                ];
                DB::commit();
            }

        } catch (QueryException $e) {
            DB::rollBack();
            $response_code = 500;
            $response = [
                'status' => '0',
                'message' => 'Database error: ' . $e->getMessage()
            ];

        } catch (\Exception $e) {
            DB::rollback();
            $response_code = 500;
            $response = [
                'status' => '0',
                'message' => 'An error occurred: ' . $e->getMessage()
            ];
        }

        return response()->json($response, $response_code);
    }

    public function forgot_password(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255|exists:users,email'
            ]);



            if ($validator->fails()) {
                $response_code = 400;
                $response = [
                    'status' => '0',
                    'message' => $validator->errors()->toArray()
                ];
            } else {
                $otp=rand(1000, 9999);
                $name=User::where('email',$request->email)->get("name")->toArray()[0]["name"];
                $user_id=User::where('email',$request->email)->get("id")->toArray()[0]["id"];
                
                $details['otp'] = $otp;
                $details['name'] = $name;
                $details['email'] = $request->email;

                // dispatch(new SendEmailJob($details));

                // $details=[
                //     "otp"=>$otp,
                //     "name"=>$name,
                //     "email"=>$request->email
                // ];
                
                // dispatch(new SendEmailJob($details));
                $email = new SendEmail($details); // Pass $this->details to the Mailable
                Mail::to($request->email)->send($email);

                $token_data=[
                    "sent_otp"=>$otp,
                    "name"=> $name,
                    "user_id"=>$user_id
                ];
                $token_data = base64_encode(serialize($token_data));
                $token = Crypt::encrypt($token_data);

                // $token_data = Crypt::decrypt($token);
                // $token_data = base64_decode(unserialize($token_data));
              
                $response_code = 200;
                $response = [
                    'status' => '1',
                    'message' => "OTP Sent Successfully",
                    "token"=>$token
                ];
            }

        }catch (\Exception $e) {
            DB::rollback();
            $response_code = 500;
            $response = [
                'status' => '0',
                'message' => 'An error occurred: ' . $e->getMessage()
            ];
        }

        return response()->json($response, $response_code);
    }

    public function verify_otp(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'otp' => 'required|numeric'
            ]);



            if ($validator->fails()) {
                $response_code = 400;
                $response = [
                    'status' => '0',
                    'message' => $validator->errors()->toArray()
                ];
            } else {
                $token_data = Crypt::decrypt($request->token);
                $token_data = unserialize(base64_decode($token_data));
                
                if($token_data["sent_otp"]==$request->otp){
                    unset($token_data["sent_otp"]);
                    $token_data = base64_encode(serialize($token_data));
                    $token = Crypt::encrypt($token_data);

                    $response_code = 200;
                    $response = [
                        'status' => '1',
                        'message' => "OTP Verified Successfully",
                        'token'=>$token
                    ];
                }
                else{
                    $response_code = 401;
                    $response = [
                        'status' => '0',
                        'message' => "OTP Does Not Match",
                    ];
                }
                
            }

        }catch (\Exception $e) {
            DB::rollback();
            $response_code = 500;
            $response = [
                'status' => '0',
                'message' => 'An error occurred: ' . $e->getMessage()
            ];
        }

        return response()->json($response, $response_code);
    }
    
    public function confirm_password(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'password' => 'required|string|min:6|regex:/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
                'password_confirmation' => 'required|same:password',
            ]);



            if ($validator->fails()) {
                $response_code = 400;
                $response = [
                    'status' => '0',
                    'message' => $validator->errors()->toArray()
                ];
            } else {
                $token_data = Crypt::decrypt($request->token);
                $token_data = unserialize(base64_decode($token_data));
                $user=User::firstWhere('id',$token_data["user_id"]);
                $user->password=Hash::make($request->password);
                $user->save();
                DB::commit();
                $response_code = 200;
                $response = [
                    'status' => '1',
                    'message' => "Password Reset Successfull"
                ];
            }

        }catch (\Exception $e) {
            DB::rollback();
            $response_code = 500;
            $response = [
                'status' => '0',
                'message' => 'An error occurred: ' . $e->getMessage()
            ];
        }

        return response()->json($response, $response_code);
    }

    

}