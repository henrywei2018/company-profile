<?php
// File: fix-broken-references.php
// Run this script from your Laravel root directory: php fix-broken-references.php

echo "🔧 Fixing broken notification references after file cleanup...\n\n";

$projectRoot = __DIR__;
$filesFixed = 0;
$totalReplacements = 0;

// Define replacement patterns
$patterns = [
    // Service class imports
    'use App\Services\ClientNotificationService;' => 'use App\Facades\Notifications;',
    'use App\Services\NotificationAlertService;' => 'use App\Facades\Notifications;',
    
    // Notification class imports (remove them)
    'use App\Notifications\NewMessageNotification;' => '// Replaced by centralized notification system',
    'use App\Notifications\DirectMessageNotification;' => '// Replaced by centralized notification system',
    'use App\Notifications\MessageAlert;' => '// Replaced by centralized notification system',
    'use App\Notifications\NewQuotationNotification;' => '// Replaced by centralized notification system',
    'use App\Notifications\QuotationStatusAlert;' => '// Replaced by centralized notification system',
    'use App\Notifications\NewChatMessageNotification;' => '// Replaced by centralized notification system',
    'use App\Notifications\ClientAlert;' => '// Replaced by centralized notification system',
    'use App\Notifications\ProjectDeadlineAlert;' => '// Replaced by centralized notification system',
    
    // Constructor dependency injection
    'ClientNotificationService $clientNotificationService' => 'NotificationService $notificationService',
    'NotificationAlertService $alertService' => 'NotificationService $notificationService',
    'NotificationAlertService $notificationAlertService' => 'NotificationService $notificationService',
    
    // Property assignments
    '$this->clientNotificationService = $clientNotificationService;' => '$this->notificationService = $notificationService;',
    '$this->alertService = $alertService;' => '$this->notificationService = $notificationService;',
    '$this->notificationAlertService = $notificationAlertService;' => '$this->notificationService = $notificationService;',
    
    // Simple method call replacements
    'new NewMessageNotification(' => "Notifications::send('message.created', ",
    'new DirectMessageNotification(' => "Notifications::send('message.created', ",
    'new MessageAlert(' => "Notifications::send('message.urgent', ",
    'new NewQuotationNotification(' => "Notifications::send('quotation.created', ",
    'new QuotationStatusAlert(' => "Notifications::send('quotation.status_updated', ",
    'new NewChatMessageNotification(' => "Notifications::send('chat.message_received', ",
    'new ProjectDeadlineAlert(' => "Notifications::send('project.deadline_approaching', ",
    'new ClientAlert(' => "Notifications::send('system.alert', ",
];

// Directories to search
$searchDirs = [
    'app/Http/Controllers',
    'app/Services',
    'app/Console/Commands',
    'app/Jobs',
    'app/Listeners',
];

// Function to recursively get PHP files
function getPhpFiles($dir) {
    $files = [];
    if (!is_dir($dir)) return $files;
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    foreach ($iterator as $file) {
        if ($file->getExtension() === 'php') {
            $files[] = $file->getPathname();
        }
    }
    
    return $files;
}

// Get all PHP files to process
$allFiles = [];
foreach ($searchDirs as $dir) {
    $fullPath = $projectRoot . '/' . $dir;
    $allFiles = array_merge($allFiles, getPhpFiles($fullPath));
}

echo "Found " . count($allFiles) . " PHP files to check...\n\n";

// Process each file
foreach ($allFiles as $filePath) {
    $originalContent = file_get_contents($filePath);
    $newContent = $originalContent;
    $fileChanged = false;
    $fileReplacements = 0;
    
    // Apply all patterns
    foreach ($patterns as $search => $replace) {
        if (strpos($newContent, $search) !== false) {
            $newContent = str_replace($search, $replace, $newContent);
            $fileChanged = true;
            $fileReplacements++;
        }
    }
    
    // Handle more complex patterns with regex
    $regexPatterns = [
        // Service method calls
        '/\$this->clientNotificationService->([a-zA-Z]+)\((.*?)\);/' => 'Notifications::send(\'$1\', $2);',
        '/\$this->alertService->([a-zA-Z]+)\((.*?)\);/' => 'Notifications::send(\'$1\', $2);',
        '/\$this->notificationAlertService->([a-zA-Z]+)\((.*?)\);/' => 'Notifications::send(\'$1\', $2);',
        
        // Notification::send patterns
        '/Notification::send\(\$([a-zA-Z]+), new NewMessageNotification\((.*?)\)\);/' => 'Notifications::send(\'message.created\', $2);',
        '/Notification::send\(\$([a-zA-Z]+), new QuotationStatusAlert\((.*?)\)\);/' => 'Notifications::send(\'quotation.status_updated\', $2);',
        '/\$([a-zA-Z]+)->notify\(new NewMessageNotification\((.*?)\)\);/' => 'Notifications::send(\'message.created\', $2);',
        '/\$([a-zA-Z]+)->notify\(new QuotationStatusAlert\((.*?)\)\);/' => 'Notifications::send(\'quotation.status_updated\', $2);',
    ];
    
    foreach ($regexPatterns as $pattern => $replacement) {
        if (preg_match($pattern, $newContent)) {
            $newContent = preg_replace($pattern, $replacement, $newContent);
            $fileChanged = true;
            $fileReplacements++;
        }
    }
    
    // Save changes if file was modified
    if ($fileChanged) {
        file_put_contents($filePath, $newContent);
        $filesFixed++;
        $totalReplacements += $fileReplacements;
        
        $relativePath = str_replace($projectRoot . '/', '', $filePath);
        echo "✅ Fixed: $relativePath ($fileReplacements replacements)\n";
    }
}

echo "\n📊 Summary:\n";
echo "Files processed: " . count($allFiles) . "\n";
echo "Files fixed: $filesFixed\n";
echo "Total replacements: $totalReplacements\n\n";

if ($filesFixed > 0) {
    echo "🎯 Next steps:\n";
    echo "1. Review the fixed files to ensure correctness\n";
    echo "2. Add 'use App\\Facades\\Notifications;' to files that need it\n";
    echo "3. Update config/app.php with NotificationServiceProvider\n";
    echo "4. Run: php artisan config:clear\n";
    echo "5. Test your application\n\n";
} else {
    echo "🎉 No broken references found! Your cleanup was successful.\n\n";
}

// Check for remaining issues
echo "🔍 Checking for remaining issues...\n";

$issuesFound = [];

foreach ($allFiles as $filePath) {
    $content = file_get_contents($filePath);
    
    // Check for remaining broken imports
    if (preg_match('/use App\\\\Services\\\\(ClientNotificationService|NotificationAlertService)/', $content)) {
        $issuesFound[] = [
            'file' => str_replace($projectRoot . '/', '', $filePath),
            'issue' => 'Still has broken service import'
        ];
    }
    
    if (preg_match('/use App\\\\Notifications\\\\(NewMessage|DirectMessage|MessageAlert|NewQuotation|QuotationStatusAlert|NewChatMessage|ClientAlert|ProjectDeadlineAlert)/', $content)) {
        $issuesFound[] = [
            'file' => str_replace($projectRoot . '/', '', $filePath),
            'issue' => 'Still has broken notification import'
        ];
    }
    
    // Check for method calls that might still reference old services
    if (preg_match('/\$(clientNotificationService|alertService|notificationAlertService)->/', $content)) {
        $issuesFound[] = [
            'file' => str_replace($projectRoot . '/', '', $filePath),
            'issue' => 'Still has old service method calls'
        ];
    }
}

if (empty($issuesFound)) {
    echo "✅ No remaining issues found!\n";
} else {
    echo "⚠️  Remaining issues to fix manually:\n";
    foreach ($issuesFound as $issue) {
        echo "  - {$issue['file']}: {$issue['issue']}\n";
    }
}

echo "\n🏁 Script completed!\n";

// File: database-cleanup.sql
?>

-- Database cleanup script for old notification references
-- Run this after fixing code references

-- Clean up old notification records
DELETE FROM notifications WHERE type IN (
    'App\\Notifications\\NewMessageNotification',
    'App\\Notifications\\DirectMessageNotification', 
    'App\\Notifications\\MessageAlert',
    'App\\Notifications\\NewQuotationNotification',
    'App\\Notifications\\QuotationStatusAlert',
    'App\\Notifications\\NewChatMessageNotification',
    'App\\Notifications\\ClientAlert',
    'App\\Notifications\\ProjectDeadlineAlert'
);

-- Clean up job queue (if using database queue)
DELETE FROM jobs WHERE payload LIKE '%NewMessageNotification%'
   OR payload LIKE '%DirectMessageNotification%'
   OR payload LIKE '%MessageAlert%'
   OR payload LIKE '%NewQuotationNotification%'
   OR payload LIKE '%QuotationStatusAlert%'
   OR payload LIKE '%NewChatMessageNotification%'
   OR payload LIKE '%ClientAlert%'
   OR payload LIKE '%ProjectDeadlineAlert%';

-- Clean up failed jobs
DELETE FROM failed_jobs WHERE payload LIKE '%NewMessageNotification%'
   OR payload LIKE '%DirectMessageNotification%'
   OR payload LIKE '%MessageAlert%' 
   OR payload LIKE '%NewQuotationNotification%'
   OR payload LIKE '%QuotationStatusAlert%'
   OR payload LIKE '%NewChatMessageNotification%'
   OR payload LIKE '%ClientAlert%'
   OR payload LIKE '%ProjectDeadlineAlert%';

<?php
echo "\n💾 Database cleanup SQL saved to database-cleanup.sql\n";
echo "Run: mysql -u username -p database_name < database-cleanup.sql\n";
?>