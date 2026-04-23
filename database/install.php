<?php
/**
 * database/install.php
 *
 * One-shot installer. Reads the appropriate schema SQL for the configured
 * driver (mysql / sqlite), executes it, and — because hashed passwords are
 * sensitive to the exact PHP build — re-hashes the two seed accounts at
 * install time with the current runtime's crypto.
 *
 * Run from the project root:
 *     php database/install.php
 *
 * ⚠ WARNING: This drops the `users`, `categories` and `posts` tables and
 *   re-creates them from scratch. Do not run against production data.
 */

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

use App\Core\Database;

$db = Database::getInstance()->pdo();

$driver = DB_DRIVER === 'sqlite' ? 'sqlite' : 'mysql';
$sqlFile = __DIR__ . "/schema.$driver.sql";
if (!is_file($sqlFile)) {
    fwrite(STDERR, "Schema file not found: $sqlFile\n");
    exit(1);
}

echo "• Loading schema from {$sqlFile}\n";
$sql = file_get_contents($sqlFile);

// Strip single-line comments so they cannot accidentally swallow the
// following statement when we split on ";\n".
$sql = preg_replace('/^\s*--[^\r\n]*$/m', '', $sql);

// Split on semicolons that terminate statements. Naive but enough for our
// own schema which contains no semicolons inside string literals.
$statements = array_filter(array_map('trim', preg_split('/;\s*[\r\n]+/', $sql)));
foreach ($statements as $stmt) {
    if ($stmt === '') continue;
    try {
        $db->exec($stmt);
    } catch (\PDOException $e) {
        // Tolerate "table does not exist" when DROP runs on a fresh DB.
        if (!str_contains($e->getMessage(), 'no such table') && !str_contains($e->getMessage(), 'Unknown table')) {
            fwrite(STDERR, "FAIL: " . $e->getMessage() . "\n  in: " . substr($stmt, 0, 80) . "...\n");
            exit(1);
        }
    }
}

// Re-hash the two seed accounts with this PHP build.
$adminHash = password_hash('Admin1234!', PASSWORD_BCRYPT, ['cost' => 12]);
$johnHash  = password_hash('Password1!', PASSWORD_BCRYPT, ['cost' => 12]);

$db->prepare("UPDATE users SET password = ? WHERE username = ?")->execute([$adminHash, 'admin']);
$db->prepare("UPDATE users SET password = ? WHERE username = ?")->execute([$johnHash,  'johndoe']);

echo "\n✔ Database installed and seeded.\n";
echo "\nSeed accounts:\n";
echo "  • admin    / Admin1234!  (Administrator)\n";
echo "  • johndoe  / Password1!  (Member)\n\n";
