<?php

namespace App\Http\Controllers;

use App\Models\PhoneVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\ClientRepository;
use Illuminate\Support\Facades\Validator;

class PublicController extends Controller
{
    //
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = User::where('phone', $request->phone)->exists();
        if(!$user){
            $user = User::create([
                'phone' => $request->phone,
                'password' => Hash::make($request->phone.'1234'),
            ]);
        } else {
            $user = $user->first();
        }
        $verificationCode = generateVerificationCode();
        $findLast = PhoneVerification::where('phone',$request->phone)->first();
        if(resend($findLast)){
            PhoneVerification::create([
                'phone' => $request->phone,
                'verification_code' => $verificationCode,
            ]);
            sendVerificationCode($user->phone, $verificationCode);

            return response()->json([
                'success' => true,
                'message' => 'Verification code has sent.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Resend sms is every 1 minutes.'
            ]);
        }

    }

    public function Verify(Request $request) {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|exists:users,phone',
            'verification_code' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'messages' => $validator->errors()
            ], 400);
        }

        $phoneVerification = PhoneVerification::where('phone', $request->phone)->first();

        if (!$phoneVerification || $request->verification_code != $phoneVerification->verification_code) {
            return response()->json(['message' => 'Invalid verification code.'], 422);
        }
        $request->merge([
            'password' => $request->phone .'1234'
        ]);
        $credentials = $request->only('phone', 'password');

        if (Auth::attempt($credentials)) {
            $phoneVerification->update([
                'verified_at' => date('Y-m-d H:i:s')
            ]);
            $user = Auth::user();

            $clientRepository = new ClientRepository();
            $client = $clientRepository->createPersonalAccessClient(
                $user->id,
                $user->name . ' Personal Access Client',
                ''
            );
            $client->makeVisible(['secret']);

            $token = $user->createToken(env('APP_NAME'))->accessToken;

            return response()->json([
                'token' => $token,
                'client_id' => $client->id,
                'client_secret' => $client->secret,
            ]);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}
