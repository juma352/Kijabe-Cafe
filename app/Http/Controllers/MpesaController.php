<?php

namespace App\Http\Controllers;

use App\Services\MpesaService;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    protected $mpesaService;

    public function __construct(MpesaService $mpesaService)
    {
        $this->mpesaService = $mpesaService;
    }

    public function registerUrls()
    {
        $response = $this->mpesaService->registerC2BUrls();

        return response()->json($response);
    }

    public function testConnection()
    {
        try {
            // Test the M-Pesa connection by checking access token
            $accessToken = $this->mpesaService->getAccessToken();
            
            if ($accessToken) {
                return response()->json([
                    'success' => true,
                    'message' => 'M-Pesa connection successful',
                    'timestamp' => now()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get M-Pesa access token'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('M-Pesa connection test failed:', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'M-Pesa connection test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function lipaNaMpesa(Request $request)
    {
        try {
            $request->validate([
                'sale_id' => 'required|exists:sales,id',
                'amount' => 'required|numeric|min:1'
            ]);

            $sale = Sale::findOrFail($request->sale_id);
            
            // Check if sale can receive payment
            if ($sale->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'This sale has already been completed.'
                ], 400);
            }

            // Validate amount matches sale total
            if ($request->amount != $sale->total) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount does not match sale total.'
                ], 400);
            }

            // Generate Lipa na M-Pesa instructions
            $paymentReference = $sale->sale_number;
            $instructions = $this->mpesaService->getLipaNaMpesaInstructions($paymentReference, $request->amount);

            Log::info('Lipa na M-Pesa instructions generated', [
                'sale_id' => $sale->id,
                'sale_number' => $sale->sale_number,
                'amount' => $request->amount
            ]);

            return response()->json([
                'success' => true,
                'instructions' => $instructions,
                'sale' => $sale,
                'message' => 'Payment instructions generated successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request data',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error generating Lipa na M-Pesa instructions', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate payment instructions. Please try again.'
            ], 500);
        }
    }

    /**
     * Handle M-Pesa callback
     */
    public function callback(Request $request)
    {
        Log::info('M-Pesa Callback received', $request->all());
        
        // Process the callback
        $response = $this->mpesaService->processCallback($request->all());
        
        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
    }

    /**
     * Handle M-Pesa confirmation
     */
    public function confirmation(Request $request)
    {
        Log::info('M-Pesa Confirmation received', $request->all());
        
        // Process the confirmation
        $response = $this->mpesaService->processConfirmation($request->all());
        
        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
    }

    /**
     * Handle M-Pesa balance result
     */
    public function balanceResult(Request $request)
    {
        Log::info('M-Pesa Balance Result received', $request->all());
        
        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
    }

    /**
     * Handle M-Pesa balance timeout
     */
    public function balanceTimeout(Request $request)
    {
        Log::info('M-Pesa Balance Timeout received', $request->all());
        
        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
    }

    public function stkPush(Request $request)
    {
        try {
            $request->validate([
                'phone' => 'required|string',
                'amount' => 'required|numeric|min:1',
                'sale_id' => 'sometimes|exists:sales,id'
            ]);

            // Generate account reference from sale or use a default
            $accountReference = 'POS-' . time();
            $transactionDesc = 'POS Payment';
            
            if ($request->has('sale_id')) {
                $sale = Sale::find($request->sale_id);
                if ($sale) {
                    $accountReference = $sale->sale_number;
                    $transactionDesc = 'Payment for Sale ' . $sale->sale_number;
                }
            }

            $response = $this->mpesaService->stkPush(
                $request->phone,
                $request->amount,
                $accountReference,
                $transactionDesc
            );

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('STK Push failed:', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'STK Push failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function stkQuery(Request $request)
    {
        try {
            $request->validate([
                'checkout_request_id' => 'required|string'
            ]);

            $response = $this->mpesaService->stkQuery($request->checkout_request_id);

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('STK Query failed:', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'STK Query failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function registerC2BUrls()
    {
        try {
            $response = $this->mpesaService->registerC2BUrls();

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Register C2B URLs failed:', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Register C2B URLs failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function fetchLiveTransactions()
    {
        try {
            // This would fetch live transactions from M-Pesa
            // Implementation depends on your M-Pesa service setup
            $response = $this->mpesaService->fetchLiveTransactions();

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Fetch live transactions failed:', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Fetch live transactions failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function validation(Request $request)
    {
        // M-Pesa validation callback
        Log::info('M-Pesa Validation Request:', $request->all());
        
        // Return success response to accept all transactions
        // You can add validation logic here if needed
        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted'
        ]);
    }
}