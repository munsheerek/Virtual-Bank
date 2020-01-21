<?php

namespace AbcBank\Http\Controllers;

use AbcBank\Deposits;
use AbcBank\Transfer;
use AbcBank\User;
use AbcBank\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $balence = $this->getBalence();
        return view('home', compact('balence'));
    }
    public function deposit() {
        return view('deposit');
    }
    public function saveDeposit(Request $request) {
        $message = [
            'min'=> 'The :attribute must be above :min INR'
        ];
        $validator = Validator::make($request->all(), [
            'amount' => 'required|min:1|regex:/^\d+(\.\d{1,2})?$/'
        ], $message)->validate();
        $deposit = new Deposits;
        $deposit->user_id = Auth::user()->id;
        $deposit->amount = $request->amount;
        $save = $deposit->save();
        if($save) {
            return back()->with('success', 'Cash deposited successfully');
        }else {
            return back()->with('error', 'Something encountered with cash deposit. Please try again');
        }
    }
    public function withdraw() {
        return view('withdraw');
    }
    public function saveWithdraw(Request $request) {
        $message = [
            'min'=> 'The :attribute must be above :min INR'
        ];
        $validator = Validator::make($request->all(), [
            'amount' => 'required|min:1|regex:/^\d+(\.\d{1,2})?$/'
        ], $message)->validate();
        $balence = $this->getBalence();
        $amount = $request->amount;
        if($balence < $amount) {
            return redirect()->back()->with('error', 'Insufficient fund to withdraw');
        }else {
            $withdraw = new Withdraw;
            $withdraw->user_id = Auth::user()->id;
            $withdraw->amount = $amount;
            $save = $withdraw->save();
            if($save) {
                return back()->with('success', 'Cash withdrawed successfully');
            }else {
                return back()->with('error', 'Something encountered with cash withdraw. Please try again');
            }
        }
    }
    public function transfer() {
        return view('transfer');
    }
    public function saveTransfer(Request $request) {
        $message = [
            'min'=> 'The :attribute must be above :min INR'
        ];
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'amount' => 'required|min:1|regex:/^\d+(\.\d{1,2})?$/'
        ], $message)->validate();
        $email = $request->email;
        $amount = $request->amount;
        $balence = $this->getBalence();
        if($email === Auth::user()->email) {
            return redirect()->back()->with('error', 'You cannot transfer fund to yourself. Please try with another registered email');
        }else {
            $email_exists = User::where('email', $email)->first();
            if(!$email_exists) {
                return redirect()->back()->with('error', 'Email address not found. Please try with registered e-mail address.');
            }else {
                $transferToEmail = $email_exists->id;
            }
            if($amount > $balence) {
                return redirect()->back()->with('error', 'Insufficient fund to transfer');
            }
            $transfer = new Transfer;
            $transfer->transferFrom = Auth::user()->id;
            $transfer->transferTo = $transferToEmail;
            $transfer->amount = $amount;
            $save = $transfer->save();
            if($save) {
                return back()->with('success', 'Fund Transfered successfully');
            }else {
                return back()->with('error', 'Something encountered with fund transfer. Please try again');
            }
        }
        
    }
    public function statement() {
        $statement = [];
        $deposit = Auth::user()->deposits()->get();
        $balence = 0;
        foreach($deposit as $d) {
            $balence += $d->amount;
            $statement[] = [
                'datetime' => $d->created_at,
                'amount' => number_format($d->amount, 2),
                'type' => 'Credit',
                'details' => 'Deposit',
                'balence' => number_format($balence, 2)
            ];
        }   
        $withdraw = Auth::user()->withdraws()->get();

        foreach($withdraw as $w) {
            $balence -= $w->amount;
            $statement[] = [
                'datetime' => $w->created_at,
                'amount' => number_format($w->amount, 2),
                'type' => 'Debit',
                'details' => 'Withdraw',
                'balence' => number_format($balence, 2)
             ];
        }
        $transfer = Transfer::where('transferFrom', Auth::user()->id)->orWhere('transferTo', Auth::user()->id)->get();
        
        $creditTransfer = $transfer->where('transferTo', Auth::user()->id);
        foreach($creditTransfer as $ct) {
            $balence += $ct->amount;
            $user = User::find($ct->transferTo);
            $statement[] = [
                'datetime' => $ct->created_at,
                'amount' => number_format($ct->amount, 2),
                'type' => 'Credit',
                'details' => 'Transfer from '.$user->email,
                'balence' => number_format($balence, 2)
            ];
        }
        $debitTransfer = $transfer->where('transferFrom', Auth::user()->id);

        foreach($debitTransfer as $dt) {
            $balence -= $dt->amount;
            $user = User::find($dt->transferTo);
            $statement[] = [
                'datetime' => $dt->created_at,
                'amount' => number_format($dt->amount, 2),
                'type' => 'Debit',
                'details' => 'Transfer to '.$user->email,
                'balence' => number_format($balence, 2)
            ];
        } 
        $statement = collect($statement)->sortBy('datetime');
        $balence = 0;
        $state = [];
        foreach($statement as $s) {
            if($s['type'] == 'Debit') {
                $balence-=$s['amount'];
            }else {
                $balence+= $s['amount'];
            }
            $s['balence'] = number_format($balence,2);
            $state[] = $s;
        }
        $statement = $state;
        return view('statement', compact('statement'));
    }
    public function getBalence() {
        $balence = 0;
        $deposit = Auth::user()->deposits()->get();
        foreach($deposit as $d) {
            $balence += $d->amount;
        }
        $withdraw = Auth::user()->withdraws()->get();
        foreach($withdraw as $w){
            $balence -= $w->amount;
        }
        $transfer = Transfer::where('transferFrom', Auth::user()->id)->orWhere('transferTo', Auth::user()->id)->get();
        $creditTransfer = $transfer->where('transferTo', Auth::user()->id);
        $debitTransfer = $transfer->where('transferFrom', Auth::user()->id);
        foreach($creditTransfer as $ct) {
            $balence += $ct->amount;
        }
        foreach($debitTransfer as $dt) {
            $balence -= $dt->amount;
        }
        return $balence;
    }
}
