<?php

namespace App\Http\Controllers\Web\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use stdClass;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payment_info = new stdClass();
        $payment_info->name = auth()->user()->name;
        $payment_info->email =  auth()->user()->email;
        $payment_info->amt = $request->price;
        return view('payment.Payumoney', ['payment_info' => $payment_info]);
    }

    public function success(Request $request)
    {
        dd($request);

        $payment = new Payment();
        $payment->post_id = '';
        $payment->package_id = '';
        $payment->payment_method_id = '';
        $payment->transaction_id = '';
        $payment->amount = '';
        $payment->active = '';
        $payment->save();

        return view('payment.PayumoneySuccess');
    }

    public function error(Request $request)
    {

        dd($request);

        // status
        // unmappedstatus
        // txnid
        // field9
        // payuMoneyId
        $payment = new Payment();
        $payment->post_id = '';
        $payment->package_id = '';
        $payment->payment_method_id = '';
        $payment->transaction_id = '';
        $payment->amount = '';
        $payment->active = '';
        $payment->save();
        return view('payment.PayumoneyFailure');
    }
}
