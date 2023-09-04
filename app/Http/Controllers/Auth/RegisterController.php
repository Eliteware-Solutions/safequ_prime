<?php

namespace App\Http\Controllers\Auth;

use App\Models\Shop;
use App\Models\User;
use App\Models\Customer;
use App\Models\Cart;
use App\Models\BusinessSetting;
use App\Models\Wallet;
use App\OtpConfiguration;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OTPVerificationController;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Cookie;
use Session;
use Nexmo;
use Twilio\Rest\Client;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'     => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        if (isset($data['email']) && filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $customer = new Customer;
            $customer->user_id = $user->id;
            $customer->save();
        } else {
            if (addon_is_activated('otp_system')) {
                $user = User::where('phone', '+' . $data['country_code'] . $data['phone'])->first();
                if ($user != null) {
                    $user->verification_code = rand(1000, 9999);
                    $user->save();

                    session()->put('is_register', 0);
                } else {
                    $referer_user = array();
                    $joined_community_id = 0;
                    if (isset($data['referer_user_id']) && intval($data['referer_user_id']) > 0) {
                        $referer_user = User::where('id', $data['referer_user_id'])->first();
                        $joined_community_id = $referer_user->joined_community_id;
                    }

                    if (isset($data['community_id']) && intval($data['community_id']) > 0) {
                        $joined_community_id = $data['community_id'];
                    }

                    if ($joined_community_id > 0) {
                        $shop = Shop::where('user_id', $joined_community_id)->first();
                        session(['link' => route('shop.visit', $shop->slug)]);
                    }

                    $user = User::create([
                        'name'                => 'Guest User',
                        'phone'               => '+' . $data['country_code'] . $data['phone'],
                        'password'            => Hash::make(123456),
                        'verification_code'   => rand(1000, 9999),
                        'balance'             => env('WELCOME_BONUS_AMOUNT'),
                        'joined_community_id' => $joined_community_id,
                        'referred_by'         => (!empty($referer_user) ? $referer_user->id : '')
                    ]);

                    $user->referral_key = md5($user->id);
                    $user->save();
                    // $this->guard()->login($user);

                    $customer = new Customer;
                    $customer->user_id = $user->id;
                    $customer->save();

                    $wallet = new Wallet;
                    $wallet->user_id = $user->id;
                    $wallet->amount = env('WELCOME_BONUS_AMOUNT');
                    $wallet->payment_method = 'bonus';
                    $wallet->payment_details = json_encode(array('id' => $user->id, 'amount' => env('WELCOME_BONUS_AMOUNT'), 'method' => 'bonus'));
                    $wallet->save();

                    if (!empty($referer_user)) {
                        $wallet = new Wallet;
                        $wallet->user_id = $referer_user->id;
                        $wallet->amount = env('REFERRAL_BONUS_AMOUNT');
                        $wallet->payment_method = 'referral_bonus';
                        $wallet->payment_details = json_encode(array('id' => $referer_user->id, 'amount' => env('REFERRAL_BONUS_AMOUNT'), 'method' => 'referral_bonus'));
                        $wallet->save();

                        $referer_user->balance = floatval($referer_user->balance) + env('REFERRAL_BONUS_AMOUNT');
                        $referer_user->save();
                    }

                    session()->put('is_register', 1);
                }

                $otpController = new OTPVerificationController;
                $otpController->send_code($user);
            }
        }

        if (session('temp_user_id') != null) {
            Cart::where('temp_user_id', session('temp_user_id'))
                ->update([
                    'user_id'      => $user->id,
                    'temp_user_id' => null
                ]);

            Session::forget('temp_user_id');
        }

        /*if (Cookie::has('referral_code')) {
            $referral_code = Cookie::get('referral_code');
            $referred_by_user = User::where('referral_code', $referral_code)->first();
            if ($referred_by_user != null) {
                $user->referred_by = $referred_by_user->id;
                $user->save();
            }
        }*/

        return $user;
    }

    public function register(Request $request)
    {
        $userData = User::where('phone', '+' . $request->country_code . $request->phone)->first();
        if ($userData != null) {
           if ($userData->banned == 1) {
               flash(translate('Cannot login, you are banned from Admin.'))->error();
               return back();
           }
        }

        /*if (filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            if (User::where('email', $request->email)->first() != null) {
                flash(translate('Email or Phone already exists.'));
                return back();
            }
        } elseif (User::where('phone', '+' . $request->country_code . $request->phone)->first() != null) {
            flash(translate('Phone already exists.'));
            return back();
        }*/

//        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

//        $this->guard()->login($user);

        /*if ($user->email != null) {
            if (BusinessSetting::where('type', 'email_verification')->first()->value != 1) {
                $user->email_verified_at = date('Y-m-d H:m:s');
                $user->save();
                flash(translate('Registration successful.'))->success();
            } else {
                try {
                    $user->sendEmailVerificationNotification();
                    flash(translate('Registration successful. Please verify your email.'))->success();
                } catch (\Throwable $th) {
                    $user->customer()->delete();
                    $user->delete();
                    flash(translate('Registration failed. Please try again later.'))->error();
                }
            }
        }*/

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }

    protected function registered(Request $request, $user)
    {
        if (addon_is_activated('otp_system') || $user->email == null) {
            return redirect()->route('verification', $user);
        } elseif (session('link') != null) {
            return redirect(session('link'));
        } else {
            return redirect()->route('home');
        }
    }
}
