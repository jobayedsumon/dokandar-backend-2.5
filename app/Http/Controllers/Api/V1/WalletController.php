<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\CustomerLogic;
use App\CentralLogics\Helpers;
use App\CentralLogics\SMS_module;
use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\User;
use App\Models\WalletBonus;
use App\Models\WalletPayment;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Library\Payer;
use App\Traits\Payment;
use App\Library\Receiver;
use App\Library\Payment as PaymentInfo;

class WalletController extends Controller
{
    public function transactions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $paginator = WalletTransaction::where('user_id', $request->user()->id)
        ->when($request['type'] && $request['type']=='order', function($query){
            $query->whereIn('transaction_type', ['order_place', 'order_refund','partial_payment']);
        })
        ->when($request['type'] && $request['type']=='loyalty_point', function($query){
            $query->whereIn('transaction_type', ['loyalty_point']);
        })
        ->when($request['type'] && $request['type']=='add_fund', function($query){
            $query->whereIn('transaction_type', ['add_fund']);
        })
        ->when($request['type'] && $request['type']=='referrer', function($query){
            $query->whereIn('transaction_type', ['referrer']);
        })
        ->latest()->paginate($request->limit, ['*'], 'page', $request->offset);

        $data = [
            'total_size' => $paginator->total(),
            'limit' => $request->limit,
            'offset' => $request->offset,
            'data' => $paginator->items()
        ];
        return response()->json($data, 200);
    }

    public function add_fund(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $customer = User::find($request->user()->id);

        $wallet_amount = $request->amount;

        if (!isset($customer)) {
            return response()->json(['errors' => ['message' => 'Customer not found']], 403);
        }

        if (!isset($wallet_amount)) {
            return response()->json(['errors' => ['message' => 'Amount not found']], 403);
        }

        if (!$request->has('payment_method')) {
            return response()->json(['errors' => ['message' => 'Payment not found']], 403);
        }

        try
        {
            $wallet = new WalletPayment();
            $wallet->user_id = $customer->id;
            $wallet->amount = $request->amount;
            $wallet->payment_status = 'pending';
            $wallet->payment_method = $request->payment_method;
            $wallet->save();

            $payer = new Payer(
                $customer->f_name . ' ' . $customer->l_name ,
                $customer->email,
                $customer->phone,
                ''
            );

            $currency=BusinessSetting::where(['key'=>'currency'])->first()->value;
            $additional_data = [
                'business_name' => BusinessSetting::where(['key'=>'business_name'])->first()?->value,
                'business_logo' => asset('storage/app/public/business') . '/' .BusinessSetting::where(['key' => 'logo'])->first()?->value
            ];
            $payment_info = new PaymentInfo(
                success_hook: 'wallet_success',
                failure_hook: 'wallet_failed',
                currency_code: $currency,
                payment_method: $request->payment_method,
                payment_platform: $request->payment_platform,
                payer_id: $customer->id,
                receiver_id: $customer->id,
                additional_data: $additional_data,
                payment_amount: $wallet_amount,
                external_redirect_link: $request->has('callback')?$request['callback']:session('callback'),
                attribute: 'wallet_payments',
                attribute_id: $wallet->id
            );

            $receiver_info = new Receiver($customer->f_name . ' ' . $customer->l_name,'example.png');

            $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);

            $data = [
                'redirect_link' => $redirect_link,
            ];

            return response()->json($data, 200);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['errors' => ['message' => 'Failed to pay for wallet add fund']], 403);
        }
    }

    public function get_bonus()
    {
        $bonuses = WalletBonus::Active()->Running()->latest()->get();
        return response()->json($bonuses??[],200);
    }

    public function fund_transfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'=>'exists:users,phone',
            'amount'=>'numeric|min:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $from_user = $request->user();

        if ($from_user->wallet_balance < $request->amount) {
            return response()->json(['errors'=>[
                'message'=> 'Insufficient balance'
            ]], 403);
        }

        $to_user = User::where('phone', $request->phone)->first();

        if ($from_user->id == $to_user->id) {
            return response()->json(['errors'=>[
                'message'=> 'You can not transfer fund to yourself'
            ]], 403);
        }

        $from_reference = $from_user->f_name.' '.$from_user->l_name . ' ('.$from_user->phone.')';
        $to_reference = $to_user->f_name.' '.$to_user->l_name . ' ('.$to_user->phone.')';

        $wallet_transaction_from = CustomerLogic::create_wallet_transaction($from_user->id, $request->amount, 'fund_transfer',$to_reference);
        $wallet_transaction_to = CustomerLogic::create_wallet_transaction($to_user->id, $request->amount, 'add_fund_by_transfer',$from_reference);

        if($wallet_transaction_from && $wallet_transaction_to)
        {
            try{

                if (isset($to_user->cm_firebase_token)) {
                    $data = [
                        'title' => 'Fund Transfer',
                        'description' => 'You have received '.$request->amount.' ৳ from '. $from_reference,
                        'order_id' => '',
                        'image' => '',
                        'type' => 'wallet_transaction',
                    ];
                    Helpers::send_push_notif_to_device($to_user->cm_firebase_token, $data);

                    DB::table('user_notifications')->insert([
                        'data' => json_encode($data),
                        'user_id' => $to_user->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                $from_sms = 'You have transferred '.$request->amount.' ৳ to ' . $to_reference . '. Your current balance is '.$from_user->wallet_balance ?? 0 .' ৳';
                $to_sms = 'You have received '.$request->amount.' ৳ from ' . $from_reference . '. Your current balance is '.$to_user->wallet_balance ?? 0 .' ৳';

                SMS_module::send_custom_sms($from_user->phone, $from_sms);
                SMS_module::send_custom_sms($to_user->phone, $to_sms);

                if(config('mail.status')) {
                    Mail::to($wallet_transaction_from->user->email)->send(new \App\Mail\AddFundToWallet($wallet_transaction_from));
                    Mail::to($wallet_transaction_to->user->email)->send(new \App\Mail\AddFundToWallet($wallet_transaction_to));
                }
            }catch(\Exception $ex)
            {
                info($ex->getMessage());
            }

            return response()->json([
                'message' => 'Fund transferred successfully',
            ]);
        }

        return response()->json(['errors'=>[
            'message'=> 'Failed to transfer fund'
        ]], 403);
    }
}
