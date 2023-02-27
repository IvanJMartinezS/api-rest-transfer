<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Account;
use App\Transactions;

class EventController extends Controller
{
    public function store(Request $request)
    {        

        if ($request->input('type') === 'deposit') {
            return $this->deposit(
                $request->input('destination'),
                $request->input('amount'),
                $request->input('type')
            );
        } elseif ($request->input('type') === 'withdraw') {
            return $this->withdraw(
                $request->input('origin'),
                $request->input('amount'),
                $request->input('type')
            );
        } elseif ($request->input('type') === 'transfer') {
            return $this->transfer(
                $request->input('origin'),
                $request->input('destination'),
                $request->input('amount'),
                $request->input('type')
            );
        }
    }
    
    private function deposit($destination, $amount, $type)
    {
        
        $account = Account::findOrFail($destination);

        $account->balance += $amount;
        $account->save();
        
        $transaction = new Transactions();

        $transaction->id_accounts = $destination;
        $transaction->amount_transaction = $amount;
        $transaction->type_transaction = $type;
        $transaction->save();

        return response()->json([
            'destination' => [
                'id' => $account->id,
                'balance' => $account->balance
            ]
        ], 201);
    }
    
    private function withdraw($origin, $amount, $type)
    {
        $account = Account::findOrFail($origin);
        
        if($amount > $account->balance){
            return response()->json([
                'Error' => [
                    'Response' => 'Saldo Insuficiente'
                ],
                'origin' => [
                    'id' => $account->id,
                    'balance' => $account->balance
                ],
            ], 404);
        }

        $account->balance -= $amount;
        $account->save();

        $transaction = new Transactions();

        $transaction->id_accounts = $origin;
        $transaction->amount_transaction = $amount;
        $transaction->type_transaction = $type;
        $transaction->save();

        return response()->json([
            'origin' => [
                'id' => $account->id,
                'balance' => $account->balance
            ]
        ], 201);
    }
    
    private function transfer($origin, $destination, $amount, $type)
    {
        $accountOrigin = Account::findOrFail($origin);
        
        $accountDestination = Account::findOrFail($destination);
        
        if($amount > $accountOrigin->balance){
            return response()->json([
                'Error' => [
                    'Response' => 'Saldo Insuficiente'
                ],
                'origin' => [
                    'id' => $accountOrigin->id,
                    'balance' => $accountOrigin->balance
                ],
                'destination' => [
                    'id' => $accountDestination->id,
                    'balance' => $accountDestination->balance
                ],
            ], 404);
        }

        DB::transaction(function () use ($accountOrigin, $accountDestination, $amount) {
            $accountOrigin->balance -= $amount;
            $accountDestination->balance += $amount;

            $accountOrigin->save();
            $accountDestination->save();
        });

        $transaction = new Transactions();

        $transaction->id_accounts = $destination;
        $transaction->amount_transaction = $amount;
        $transaction->type_transaction = $type;
        $transaction->save();

        return response()->json([
            'origin' => [
                'id' => $accountOrigin->id,
                'balance' => $accountOrigin->balance
            ],
            'destination' => [
                'id' => $accountDestination->id,
                'balance' => $accountDestination->balance
            ],
        ], 201);
    } 
}
