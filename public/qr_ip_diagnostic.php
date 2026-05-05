<?php
// Network IP detection for QR code generation
$wifi_ip = '192.168.0.207';  // Your WiFi/Network IP from ipconfig
$vpn_ip = '10.14.0.2';        // VPN IP (won't work for local network access)
$hostname = gethostname();

// Determine the best IP to use
$server_ip = $hostname . '.local';  // Use hostname which resolves on local network
$verify_url = 'http://' . $server_ip . '/public/verify_ticket.php?ticket_id=9&code=TICKET-9';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code - IP Address Diagnostic</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 900px;
            margin: 30px auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
        }
        
        h1 {
            color: #667eea;
            margin-bottom: 30px;
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
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-box p {
            color: #666;
            word-break: break-all;
            font-family: monospace;
            background: white;
            padding: 12px;
            border-radius: 4px;
            font-size: 0.9rem;
            line-height: 1.6;
        }
        
        .ip-detection {
            background: #fff3cd;
            border-left-color: #ffc107;
        }
        
        .qr-section {
            text-align: center;
            margin: 30px 0;
            padding: 30px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .qr-section img {
            max-width: 300px;
            border: 3px solid #667eea;
            border-radius: 8px;
            padding: 10px;
            background: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .qr-label {
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
            font-size: 1.1rem;
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
        
        .important {
            background: #e7f3ff;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #0072ff;
            margin-top: 30px;
        }
        
        .important h3 {
            color: #0072ff;
            margin-bottom: 10px;
        }
        
        .important ol {
            margin-left: 20px;
            color: #333;
            line-height: 1.8;
        }
        
        .important li {
            margin-bottom: 8px;
        }
        
        .success {
            background: #d4edda;
            border-left-color: #28a745;
            color: #155724;
        }
        
        .warning {
            background: #fff3cd;
            border-left-color: #ffc107;
            color: #856404;
        }
        
        .error {
            background: #f8d7da;
            border-left-color: #dc3545;
            color: #721c24;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>🔲 QR Code - Server IP Address Configuration</h1>
    
    <div class="info-box warning">
        <label>⚠️ Problem Identified:</label>
        <p>You're running <strong>Surfshark VPN</strong>, which gives you the IP 10.14.0.2 (tunnel IP). Your phone can't access a VPN tunnel IP from outside the VPN network!</p>
    </div>
    
    <div class="info-box success">
        <label>✅ Solution Applied:</label>
        <p>The QR code now uses your <strong>hostname (Bespok.local)</strong> which automatically resolves to your correct WiFi IP <strong>192.168.0.207</strong> on your local network!</p>
    </div>
    
    <div class="info-box ip-detection">
        <label>🔍 Your Network Configuration:</label>
        <p><strong>Hostname:</strong> <?php echo htmlspecialchars($hostname); ?></p>
        <p><strong>WiFi/Network IP:</strong> <?php echo htmlspecialchars($wifi_ip); ?> ✓ (This is what you need!)</p>
        <p><strong>VPN Tunnel IP:</strong> <?php echo htmlspecialchars($vpn_ip); ?> ✗ (This won't work from local network)</p>
        <p style="border-top: 2px solid #ffc107; margin-top: 15px; padding-top: 15px;"><strong style="color: #ff6f00;">✓ FINAL HOSTNAME USED IN QR CODE:</strong> <?php echo htmlspecialchars($server_ip); ?></p>
    </div>
    
    <div class="qr-section">
        <div class="qr-label">🔲 Scan This NEW QR Code (Works on WiFi!)</div>
        <p style="color: #666; margin-bottom: 15px;">This QR now uses your hostname which resolves on your local WiFi network:</p>
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=<?php echo urlencode($verify_url); ?>" alt="Updated QR Code with Hostname">
        <p style="color: #666; margin-top: 15px; font-size: 0.9rem;">QR encodes URL: <br><code style="background: white; padding: 8px 12px; border-radius: 4px; display: inline-block; margin-top: 10px;"><?php echo htmlspecialchars($verify_url); ?></code></p>
        
        <div class="button-group">
            <a href="<?php echo htmlspecialchars($verify_url); ?>" class="btn btn-primary">
                🔗 Test URL Directly (on same network)
            </a>
            <a href="http://busticketingsystem.test/public/verify_ticket.php?ticket_id=9&code=TICKET-9" class="btn btn-success">
                💻 Test Locally (same computer)
            </a>
        </div>
    </div>
    
    <div class="important">
        <h3>📱 How to Test on Your Phone Now (IMPORTANT!):</h3>
        <ol>
            <li><strong>Make sure VPN is DISCONNECTED:</strong> Disconnect from Surfshark VPN so your phone is on your normal WiFi</li>
            <li><strong>Connect phone to WiFi:</strong> Connect your phone to your WiFi network (same as your computer)</li>
            <li><strong>Take a screenshot:</strong> Screenshot this page showing the QR code</li>
            <li><strong>Scan the QR:</strong> Open your phone camera and scan the QR code from the screenshot</li>
            <li><strong>Tap the link:</strong> When a notification appears, tap it to open</li>
            <li><strong>View details:</strong> The verification page will show all passenger details!</li>
        </ol>
    </div>
    
    <div class="info-box">
        <label>💡 Important Notes:</label>
        <p>
            • Your phone MUST be on the SAME WiFi network as your computer<br>
            • Your computer MUST have Laragon/Apache running<br>
            • VPN should be DISCONNECTED for local network testing<br>
            • The hostname "<?php echo htmlspecialchars($hostname); ?>.local" resolves on your local network<br>
            • All incoming ticket QR codes will automatically use this hostname<br>
            • When deployed to a real server, this will automatically use the server's domain
        </p>
    </div>
</div>
</body>
</html>
