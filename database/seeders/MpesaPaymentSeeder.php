<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MpesaPayment;
use Carbon\Carbon;

class MpesaPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample M-Pesa payments based on the screenshot
        $payments = [
            [
                'transaction_code' => 'TK4B99917A',
                'customer_name' => 'REGINA',
                'phone_number' => '2547****570',
                'amount' => 60.00,
                'transaction_time' => Carbon::create(2025, 11, 4, 10, 57, 38),
            ],
            [
                'transaction_code' => 'TK4BI989Q8',
                'customer_name' => 'ASHLEY',
                'phone_number' => '2541****283',
                'amount' => 60.00,
                'transaction_time' => Carbon::create(2025, 11, 4, 10, 57, 36),
            ],
            [
                'transaction_code' => 'TK46S959QE',
                'customer_name' => 'AMY',
                'phone_number' => '2547****854',
                'amount' => 60.00,
                'transaction_time' => Carbon::create(2025, 11, 4, 10, 56, 31),
            ],
            [
                'transaction_code' => 'TK4K195S06',
                'customer_name' => 'JOYCE',
                'phone_number' => '2547****753',
                'amount' => 10.00,
                'transaction_time' => Carbon::create(2025, 11, 4, 10, 56, 16),
            ],
            [
                'transaction_code' => 'TK4B897SIW',
                'customer_name' => 'David',
                'phone_number' => '2547****755',
                'amount' => 60.00,
                'transaction_time' => Carbon::create(2025, 11, 4, 10, 56, 7),
            ],
            [
                'transaction_code' => 'TK4N297ODV',
                'customer_name' => 'Stephen',
                'phone_number' => '2547****252',
                'amount' => 50.00,
                'transaction_time' => Carbon::create(2025, 11, 4, 10, 54, 32),
            ],
            [
                'transaction_code' => 'TK49C93UYX',
                'customer_name' => 'LINNET',
                'phone_number' => '2547****688',
                'amount' => 60.00,
                'transaction_time' => Carbon::create(2025, 11, 4, 10, 51, 23),
            ],
            [
                'transaction_code' => 'TK40397P1L',
                'customer_name' => 'ANITA',
                'phone_number' => '2547****770',
                'amount' => 80.00,
                'transaction_time' => Carbon::create(2025, 11, 4, 8, 37, 12),
            ],
            [
                'transaction_code' => 'TK4PN94TAU',
                'customer_name' => 'Carolyne',
                'phone_number' => '2547****821',
                'amount' => 450.00,
                'transaction_time' => Carbon::create(2025, 11, 4, 7, 29, 6),
            ],
            [
                'transaction_code' => 'TK4N495NR5',
                'customer_name' => 'antony',
                'phone_number' => '2547****802',
                'amount' => 400.00,
                'transaction_time' => Carbon::create(2025, 11, 4, 7, 28, 17),
            ],
            [
                'transaction_code' => 'TK4HY99QRR',
                'customer_name' => 'DOMINIC',
                'phone_number' => '2547****744',
                'amount' => 50.00,
                'transaction_time' => Carbon::create(2025, 11, 4, 7, 20, 26),
            ],
            // Add some more recent payments for testing
            [
                'transaction_code' => 'TK4X123ABC',
                'customer_name' => 'JOHN',
                'phone_number' => '2547****123',
                'amount' => 70.00,
                'transaction_time' => Carbon::now()->subMinutes(5),
            ],
            [
                'transaction_code' => 'TK4Y456DEF',
                'customer_name' => 'MARY',
                'phone_number' => '2547****456',
                'amount' => 100.00,
                'transaction_time' => Carbon::now()->subMinutes(10),
            ],
            [
                'transaction_code' => 'TK4Z789GHI',
                'customer_name' => 'PETER',
                'phone_number' => '2547****789',
                'amount' => 250.00,
                'transaction_time' => Carbon::now()->subMinutes(15),
            ],
        ];

        foreach ($payments as $payment) {
            MpesaPayment::updateOrCreate(
                ['transaction_code' => $payment['transaction_code']], // Find by transaction_code
                [
                    'customer_name' => $payment['customer_name'],
                    'phone_number' => $payment['phone_number'],
                    'amount' => $payment['amount'],
                    'transaction_time' => $payment['transaction_time'],
                    'status' => 'pending',
                ]
            );
        }

        echo "Created/Updated " . count($payments) . " M-Pesa payment records\n";
    }
}
