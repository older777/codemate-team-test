<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountOperationRequest;
use App\Http\Services\AccountOperationServiсe;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AccountOperation extends Controller
{
    /**
     * Депозит средств
     */
    public function deposit(AccountOperationRequest $request): JsonResponse
    {
        try {
            $service = new AccountOperationServiсe($request);
            $message = $service->makeDeposit();

            return response()->json([
                'success' => true,
                'message' => $message,
            ], Response::HTTP_OK, [], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Вывод средств
     */
    public function withdraw(AccountOperationRequest $request): JsonResponse
    {
        try {
            $service = new AccountOperationServiсe($request);
            $message = $service->makeWithdraw();

            return response()->json([
                'success' => true,
                'message' => $message,
            ], Response::HTTP_OK, [], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $code = $e->getCode() ? $e->getCode() : Response::HTTP_UNPROCESSABLE_ENTITY;

            return response()->json([
                'success' => false,
                'errors' => $e->getMessage(),
            ], $code, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Перевод пользователю
     */
    public function transfer(AccountOperationRequest $request): JsonResponse
    {
        try {
            $service = new AccountOperationServiсe($request);
            $message = $service->makeTransfer();

            return response()->json([
                'success' => true,
                'message' => $message,
            ], Response::HTTP_OK, [], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $code = $e->getCode() ? $e->getCode() : Response::HTTP_UNPROCESSABLE_ENTITY;

            return response()->json([
                'success' => false,
                'errors' => $e->getMessage(),
            ], $code, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Получить баланс пользователя
     */
    public function getBalance(AccountOperationRequest $request, User $user): JsonResponse
    {
        try {
            $service = new AccountOperationServiсe($request);
            $balance = $service->getBalance($user);

            return response()->json([
                'success' => true,
                'user_id' => $user->id,
                'balance' => $balance,
            ], Response::HTTP_OK, [], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
