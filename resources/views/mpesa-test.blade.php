<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M-Pesa Test Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; padding: 2rem; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .btn { padding: 1rem 2rem; margin: 0.5rem; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-warning { background: #ffc107; color: black; }
        .btn-info { background: #17a2b8; color: white; }
        .result { margin-top: 1rem; padding: 1rem; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        input, select { padding: 0.75rem; margin: 0.5rem; border: 1px solid #ddd; border-radius: 4px; width: 200px; }
        .config-info { background: #f8f9fa; padding: 1rem; border-radius: 4px; margin-bottom: 2rem; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏥 Kijabe Hospital M-Pesa Test Dashboard</h1>
        
        <div class="config-info">
            <h3>Current M-Pesa Configuration:</h3>
            <p><strong>Environment:</strong> {{ config('mpesa.environment') }}</p>
            <p><strong>Shortcode:</strong> {{ config('mpesa.shortcode') }}</p>
            <p><strong>Consumer Key:</strong> {{ substr(config('mpesa.consumer_key'), 0, 10) }}...</p>
        </div>

        <div style="margin-bottom: 2rem;">
            <h3>🔧 Connection Tests</h3>
            <button class="btn btn-primary" onclick="testConnection()">Test M-Pesa Connection</button>
            <button class="btn btn-warning" onclick="registerUrls()">Register URLs with Safaricom</button>
            <button class="btn btn-info" onclick="debugMpesa()">Debug M-Pesa Configuration</button>
        </div>

        <div style="margin-bottom: 2rem;">
            <h3>📱 STK Push Test</h3>
            <input type="tel" id="testPhone" placeholder="Phone Number (254712345678)" />
            <input type="number" id="testAmount" placeholder="Amount (e.g., 1)" min="1" />
            <button class="btn btn-success" onclick="testStkPush()">Send Test STK Push</button>
        </div>

        <div style="margin-bottom: 2rem;">
            <h3>💳 Lipa na M-Pesa Test</h3>
            <input type="number" id="lipaNaAmount" placeholder="Amount (e.g., 100)" min="1" />
            <button class="btn btn-success" onclick="testLipaNaMpesa()">Generate Lipa na M-Pesa Instructions</button>
        </div>

        <div id="results"></div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        async function testConnection() {
            showResult('Testing M-Pesa connection...', 'info');
            
            try {
                const response = await fetch('/api/payments/test-connection', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                const responseText = await response.text();
                console.log('Test connection raw response:', responseText);
                
                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (parseError) {
                    showResult('❌ Server returned invalid response. Response: ' + responseText, 'error');
                    console.error('Parse error:', parseError);
                    return;
                }
                
                if (result.success) {
                    let successMsg = '✅ M-Pesa connection successful!<br>';
                    successMsg += 'Environment: ' + result.environment + '<br>';
                    successMsg += 'Shortcode: ' + result.shortcode + '<br>';
                    successMsg += 'Token: ' + result.token_preview;
                    showResult(successMsg, 'success');
                } else {
                    let errorMsg = '❌ M-Pesa connection failed!<br>';
                    errorMsg += 'Error: ' + result.error + '<br>';
                    if (result.troubleshooting) {
                        errorMsg += '<br>Troubleshooting:<br>';
                        result.troubleshooting.forEach(tip => {
                            errorMsg += '• ' + tip + '<br>';
                        });
                    }
                    showResult(errorMsg, 'error');
                }
                
            } catch (error) {
                showResult('❌ Network error: ' + error.message, 'error');
                console.error('Connection test error:', error);
            }
        }

        async function registerUrls() {
            showResult('Registering URLs with Safaricom...', 'info');
            
            try {
                const response = await fetch('/api/payments/register-urls', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                const responseText = await response.text();
                console.log('Register URLs raw response:', responseText);
                
                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (parseError) {
                    showResult('❌ Server returned invalid response. Response: ' + responseText, 'error');
                    console.error('Parse error:', parseError);
                    return;
                }
                
                if (result.ResponseDescription) {
                    showResult('📝 URL Registration Response:<br>' + result.ResponseDescription, 'success');
                } else {
                    showResult('❌ URL Registration Failed!<br>' + JSON.stringify(result), 'error');
                }
                
            } catch (error) {
                showResult('❌ Network error: ' + error.message, 'error');
                console.error('Register URLs error:', error);
            }
        }

        async function testStkPush() {
            const phone = document.getElementById('testPhone').value;
            const amount = document.getElementById('testAmount').value;
            
            if (!phone || !amount) {
                showResult('Please enter both phone number and amount', 'error');
                return;
            }
            
            showResult('Sending STK Push to ' + phone + ' for KSh ' + amount + '...', 'info');
            
            try {
                // First create a test sale (simplified for testing)
                const saleData = {
                    items: [{ product_id: 1, quantity: 1 }],
                    customer_phone: phone
                };
                
                const saleResponse = await fetch('/pos/create-sale', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(saleData)
                });
                
                const saleResult = await saleResponse.json();
                
                if (!saleResult.success) {
                    showResult('Failed to create test sale: ' + saleResult.message, 'error');
                    return;
                }
                
                // Now send STK Push
                const stkResponse = await fetch('/mpesa/stk-push', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        phone: phone,
                        amount: amount,
                        sale_id: saleResult.sale.id
                    })
                });
                
                const stkResult = await stkResponse.json();
                
                if (stkResult.success) {
                    showResult('📱 STK Push Sent Successfully!<br>Checkout Request ID: ' + stkResult.checkoutRequestId + '<br>Check your phone for M-Pesa prompt', 'success');
                } else {
                    showResult('❌ STK Push Failed!<br>' + stkResult.message, 'error');
                }
                
            } catch (error) {
                showResult('Error: ' + error.message, 'error');
            }
        }

        async function testLipaNaMpesa() {
            const amount = document.getElementById('lipaNaAmount').value;
            
            if (!amount || amount <= 0) {
                showResult('Please enter a valid amount', 'error');
                return;
            }
            
            showResult('Generating Lipa na M-Pesa instructions for KSh ' + amount + '...', 'info');
            
            try {
                // First create a test sale
                const saleResponse = await fetch('/api/payments/create-test-sale', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                const saleResponseText = await saleResponse.text();
                console.log('Sale creation raw response:', saleResponseText);
                
                let saleResult;
                try {
                    saleResult = JSON.parse(saleResponseText);
                } catch (parseError) {
                    showResult('❌ Failed to create test sale. Response: ' + saleResponseText, 'error');
                    console.error('Sale parse error:', parseError);
                    return;
                }
                
                if (!saleResult.success) {
                    showResult('Failed to create test sale: ' + saleResult.message, 'error');
                    return;
                }
                
                // Now generate Lipa na M-Pesa instructions
                const lipaNaResponse = await fetch('/api/payments/lipa-na-mpesa', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        sale_id: saleResult.sale.id,
                        amount: amount
                    })
                });
                
                const responseText = await lipaNaResponse.text();
                console.log('Raw response:', responseText);
                
                let lipaNaResult;
                try {
                    lipaNaResult = JSON.parse(responseText);
                } catch (parseError) {
                    showResult('❌ Server returned invalid response. Check console for details.', 'error');
                    console.error('Parse error:', parseError);
                    console.error('Response text:', responseText);
                    return;
                }
                
                if (lipaNaResult.success) {
                    let instructionsHtml = '💳 <strong>Lipa na M-Pesa Instructions Generated!</strong><br><br>';
                    instructionsHtml += '<strong>Paybill Number:</strong> ' + lipaNaResult.instructions.paybill + '<br>';
                    instructionsHtml += '<strong>Account Number:</strong> ' + lipaNaResult.instructions.account_number + '<br>';
                    instructionsHtml += '<strong>Amount:</strong> KSh ' + lipaNaResult.instructions.amount.toLocaleString() + '<br><br>';
                    instructionsHtml += '<strong>Customer Instructions:</strong><ol>';
                    
                    lipaNaResult.instructions.instructions.forEach(function(instruction) {
                        instructionsHtml += '<li>' + instruction + '</li>';
                    });
                    
                    instructionsHtml += '</ol>';
                    
                    showResult(instructionsHtml, 'success');
                } else {
                    showResult('❌ Failed to generate Lipa na M-Pesa instructions!<br>' + lipaNaResult.message, 'error');
                }
                
            } catch (error) {
                showResult('Error: ' + error.message, 'error');
                console.error('Full error:', error);
            }
        }

        async function debugMpesa() {
            showResult('Getting M-Pesa debug information...', 'info');
            
            try {
                const response = await fetch('/api/payments/debug');
                const result = await response.json();
                
                let debugHtml = '🔍 <strong>M-Pesa Configuration Debug</strong><br><br>';
                
                // Show configuration
                debugHtml += '<strong>Configuration:</strong><br>';
                Object.entries(result.config).forEach(([key, value]) => {
                    debugHtml += `${key}: ${value}<br>`;
                });
                
                // Show API test results
                if (result.api_test) {
                    debugHtml += '<br><strong>API Test Results:</strong><br>';
                    debugHtml += `URL: ${result.api_test.url}<br>`;
                    debugHtml += `Status: ${result.api_test.status}<br>`;
                    debugHtml += `Successful: ${result.api_test.successful}<br>`;
                    
                    if (result.api_test.body) {
                        debugHtml += '<br><strong>Response:</strong><br>';
                        debugHtml += '<pre>' + JSON.stringify(result.api_test.body, null, 2) + '</pre>';
                    }
                }
                
                // Show errors if any
                if (result.error) {
                    debugHtml += '<br><strong>Error:</strong><br>';
                    debugHtml += result.error;
                }
                
                showResult(debugHtml, result.error ? 'error' : 'success');
                
            } catch (error) {
                showResult('Error getting debug info: ' + error.message, 'error');
            }
        }

        function showResult(message, type) {
            const resultsDiv = document.getElementById('results');
            const timestamp = new Date().toLocaleTimeString();
            resultsDiv.innerHTML = `<div class="result ${type}"><strong>[${timestamp}]</strong><br>${message}</div>` + resultsDiv.innerHTML;
        }
    </script>
</body>
</html>