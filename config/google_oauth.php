<?php
// สร้าง OAuth 2.0 Client แบบ "Web application" ใน Google Cloud Console แล้วใส่ค่าที่นี่
// Redirect URI ที่ต้องเพิ่มใน Google Console: http://localhost/hotel-of-smai-village/google_callback.php
return [
    'client_id' => 'PUT_YOUR_GOOGLE_CLIENT_ID_HERE',
    'client_secret' => 'PUT_YOUR_GOOGLE_CLIENT_SECRET_HERE',
    'redirect_uri' => 'http://localhost/hotel-of-smai-village/google_callback.php',
];
