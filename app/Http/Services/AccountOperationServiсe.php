<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\UserAccount;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class AccountOperationServiсe
{
    protected $request;

    public function __construct(Request $request)
    {
        if ($request->has('amount')) {
            preg_match('/\d*\.\d{2}/', $request->amount, $matchs, PREG_UNMATCHED_AS_NULL);
            if (count($matchs)) {
                $request->merge([
                    'amount' => (float) $matchs[0],
                ]);
            }
        }
        $this->request = $request;
    }

    /**
     * Пополнение счёта
     */
    public function makeDeposit(): string
    {
        DB::beginTransaction();

        $userAccount = UserAccount::where('user_id', $this->request->user_id)
            ->orderByDesc('id')
            ->first();

        if ($userAccount) {
            $balance = number_format($userAccount->balance + $this->request->amount, 2);
        } else {
            $balance = $this->request->amount;
        }

        UserAccount::create(array_merge([
            'operation' => 'deposit',
            'balance' => $balance,
        ],
            $this->request->all()));

        DB::commit();

        return 'Операция выполнена';
    }

    /**
     * Вывод средств со счёта
     */
    public function makeWithdraw(): string
    {
        DB::beginTransaction();

        $userAccount = UserAccount::where('user_id', $this->request->user_id)
            ->orderByDesc('id')
            ->first();

        if ($userAccount && number_format($userAccount->balance - $this->request->amount, 2) >= 0) {
            $balance = number_format($userAccount->balance - $this->request->amount, 2);
        } else {
            throw new Exception('Не достаточно средств для вывода!', Response::HTTP_CONFLICT);
        }

        UserAccount::create(array_merge([
            'operation' => 'withdraw',
            'balance' => $balance,
        ],
            $this->request->all()));

        DB::commit();

        return 'Операция выполнена';
    }

    /**
     * Перевод средств
     */
    public function makeTransfer(): string
    {
        DB::beginTransaction();

        $userAccountFrom = UserAccount::where('user_id', $this->request->from_user_id)
            ->orderByDesc('id')
            ->first();

        if ($userAccountFrom && number_format($userAccountFrom->balance - $this->request->amount, 2) >= 0) {
            $balanceFrom = number_format($userAccountFrom->balance - $this->request->amount, 2);
        } else {
            throw new Exception('Не достаточно средств для перевода!', Response::HTTP_CONFLICT);
        }

        // расход средств у from_user_id
        UserAccount::create([
            'user_id' => $this->request->from_user_id,
            'operation' => 'transfer_out',
            'amount' => $this->request->amount,
            'balance' => $balanceFrom,
            'comment' => $this->request->comment,
        ]);

        $userAccountTo = UserAccount::where('user_id', $this->request->to_user_id)
            ->orderByDesc('id')
            ->first();

        if ($userAccountTo) {
            $balanceTo = number_format($userAccountTo->balance + $this->request->amount, 2);
        } else {
            $balanceTo = $this->request->amount;
        }

        // поступление средств у to_user_id
        UserAccount::create([
            'user_id' => $this->request->to_user_id,
            'operation' => 'transfer_in',
            'amount' => $this->request->amount,
            'balance' => $balanceTo,
            'comment' => $this->request->comment." (от пользователя ID: {$this->request->from_user_id})",
        ]);

        DB::commit();

        return 'Операция выполнена';
    }

    /**
     * Баланс пользователя
     */
    public function getBalance(User $user): float
    {
        $userAccount = UserAccount::where('user_id', $user->id)
            ->orderByDesc('id')
            ->first();

        if ($userAccount) {
            return $userAccount->balance;
        }

        return 0;
    }
}
