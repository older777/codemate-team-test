<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AccountOperationTest extends TestCase
{
    /**
     * Deposit test
     */
    #[DataProvider('dataProvide')]
    public function test_deposit(array $array): void
    {
        DB::beginTransaction();
        $response = $this->postJson(route('deposit'), $array);
        DB::rollBack();

        if (! $array['fail']) {
            $response->assertOk();
        } else {
            $response->assertUnprocessable();
        }
    }

    /**
     * Data provider
     */
    public static function dataProvide(): array
    {
        return [
            [
                [
                    'user_id' => 1,
                    'amount' => 500.00,
                    'comment' => 'Пополнение через карту',
                    'fail' => false,
                ],
            ],
            [
                [
                    'amount' => 500.00,
                    'comment' => 'Пополнение через карту',
                    'fail' => true,
                ],
            ],
        ];
    }
}
