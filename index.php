<?php
// קובץ index.php - בתיקייה הראשית

$path = $_SERVER['REQUEST_URI'];
$shortCode = trim($path, '/');

// אם זה עמוד הבית
if (empty($shortCode)) {
    echo '<h1 style="text-align:center; color:#2c5aa0;">🔗 GILBOA.LINK</h1>';
    echo '<p style="text-align:center; color:#666;">שירות קיצור קישורים של מועצה אזורית הגלבוע</p>';
    exit;
}

// הגדרות Google Sheets
$spreadsheetId = '1YqXyxKGIjXSHQgN8ok2JKbBf87SIMaBMWCJ72-NSbw8';
$apiKey = 'AIzaSyAwPN3u1IUd3HL3Q7LwJKemep8dBsU_R5M';

// קריאה לגוגל שיטס
$url = "https://sheets.googleapis.com/v4/spreadsheets/{$spreadsheetId}/values/URL!A:G?key={$apiKey}";
$response = @file_get_contents($url);

if ($response !== false) {
    $data = json_decode($response, true);
    
    if (isset($data['values'])) {
        foreach ($data['values'] as $rowIndex => $row) {
            if ($rowIndex == 0) continue; // דלג על כותרת
            
            // בדוק אם עמודה C (נושא מעובד) מכילה את הקוד שלנו
            $processedSubject = str_replace(' ', '-', $row[2] ?? '');
            
            if ($processedSubject == $shortCode || $row[2] == $shortCode) {
                if (isset($row[1]) && !empty($row[1])) {
                    // עדכון מספר קליקים (אופציונלי)
                    header("Location: " . $row[1], true, 301);
                    exit;
                }
            }
        }
    }
}

header("HTTP/1.0 404 Not Found");
echo '<h1 style="text-align:center; color:#d63384;">❌ הקישור לא נמצא</h1>';
echo '<p style="text-align:center;"><a href="https://hagilboa.link" style="color:#2c5aa0;">חזור לעמוד הבית</a></p>';
?>