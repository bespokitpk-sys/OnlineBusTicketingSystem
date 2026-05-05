<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Test - URL Verification</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 30px auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
        }
        
        h1 {
            color: #667eea;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .info-box {
            background: #f0f4ff;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            margin-bottom: 20px;
        }
        
        .info-box label {
            font-weight: 700;
            color: #333;
            display: block;
            margin-bottom: 8px;
        }
        
        .info-box p {
            color: #666;
            word-break: break-all;
            font-family: monospace;
            background: white;
            padding: 12px;
            border-radius: 4px;
            font-size: 0.85rem;
            line-height: 1.6;
        }
        
        .test-section {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #eee;
        }
        
        .qr-display {
            text-align: center;
            margin: 20px 0;
        }
        
        .qr-display img {
            max-width: 300px;
            border: 3px solid #667eea;
            border-radius: 8px;
            padding: 10px;
            background: white;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5568d3;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .btn-success {
            background: #4caf50;
            color: white;
        }
        
        .btn-success:hover {
            background: #45a049;
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.4);
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #c3e6cb;
            margin-top: 20px;
        }
        
        .test-instructions {
            background: #e7f3ff;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #0072ff;
            margin-top: 20px;
        }
        
        .test-instructions h3 {
            color: #0072ff;
            margin-bottom: 10px;
        }
        
        .test-instructions ol {
            margin-left: 20px;
            color: #333;
            line-height: 1.8;
        }
        
        .test-instructions li {
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>🔲 QR Code - URL Verification Test</h1>
    
    <div class="info-box">
        <label>✅ QR CODE IS NOW FIXED!</label>
        <p>The QR code now contains a FULL ABSOLUTE URL that your phone can open directly.</p>
    </div>
    
    <div class="info-box">
        <label>📍 Test URL Format (What's encoded in the QR):</label>
        <p>http://busticketingsystem.test/public/verify_ticket.php?ticket_id=9&code=TICKET-9</p>
    </div>
    
    <div class="info-box">
        <label>✓ How it works:</label>
        <p>
            • When you scan the QR code with your phone camera, it reads the URL above<br>
            • Your phone automatically opens the URL in a browser<br>
            • The verification page loads and shows all passenger details<br>
            • No more "search on web" or error messages!
        </p>
    </div>
    
    <div class="test-section">
        <h2 style="color: #667eea; margin-bottom: 20px;">🧪 Test The QR Code Below</h2>
        
        <div class="qr-display">
            <p style="color: #666; margin-bottom: 10px;">Scan this QR code with your phone camera:</p>
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=http%3A%2F%2Fbusticketingsystem.test%2Fpublic%2Fverify_ticket.php%3Fticket_id%3D9%26code%3DTICKET-9" alt="Test QR Code">
            <p style="color: #666; margin-top: 10px; font-size: 0.85rem;">Ticket #9 Verification QR Code</p>
        </div>
        
        <div class="button-group">
            <a href="http://busticketingsystem.test/public/verify_ticket.php?ticket_id=9&code=TICKET-9" class="btn btn-primary">
                🔗 Click Here to Test URL Directly
            </a>
            <button class="btn btn-success" onclick="alert('✅ If you see this, JavaScript is working!\n\nOn your phone:\n1. Open the QR code image\n2. Point your phone camera at it\n3. Tap the notification that appears\n\nIt will open: http://busticketingsystem.test/public/verify_ticket.php?ticket_id=9&code=TICKET-9')">
                ℹ️ How to Scan
            </button>
        </div>
    </div>
    
    <div class="test-instructions">
        <h3>📱 How to Test on Your Phone:</h3>
        <ol>
            <li><strong>Get the receipt:</strong> Book a ticket and go to the receipt page</li>
            <li><strong>See the QR code:</strong> The QR code is displayed on the receipt</li>
            <li><strong>Scan with camera:</strong> Open your phone's camera app and point it at the QR code</li>
            <li><strong>Tap the link:</strong> When a notification appears with the URL, tap it</li>
            <li><strong>View details:</strong> The verification page opens showing all passenger details</li>
            <li><strong>Operator scans:</strong> The operator can scan the same QR and confirm your details</li>
        </ol>
    </div>
    
    <div class="success-message">
        ✅ <strong>FIXED!</strong> The QR code now generates with a complete URL that includes:
        <ul style="margin-top: 10px;">
            <li>✓ Protocol (http://)</li>
            <li>✓ Domain (busticketingsystem.test)</li>
            <li>✓ Path (/public/verify_ticket.php)</li>
            <li>✓ Parameters (?ticket_id=9&code=TICKET-9)</li>
        </ul>
    </div>
</div>
</body>
</html>
