<?php

declare(strict_types=1);

use App\Core\Auth;

/**
 * Global helper functions.
 *
 * These are loaded once by bootstrap.php and are available everywhere — views,
 * controllers, models. Keep them stateless and short; anything with real logic
 * belongs in a class.
 */

// ── Output ────────────────────────────────────────────────────────────────────

/** Escape a value for safe HTML output. */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Echo an escaped value. Shorthand for `echo e($value)`. */
function esc(mixed $value): void
{
    echo e($value);
}

// ── URLs ──────────────────────────────────────────────────────────────────────

/** Build a fully-qualified application URL from a path. */
function url(string $path = '/'): string
{
    $path = '/' . ltrim($path, '/');
    return rtrim(ROOT_URL, '/') . $path;
}

/** Build a URL for an uploaded file (thumbnail, avatar, etc). */
function upload_url(string $filename): string
{
    return url('uploads/' . ltrim($filename, '/'));
}

/** Build a URL for a static asset in /public/assets. */
function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

/** Redirect to a URL and halt. */
function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

// ── Request helpers ───────────────────────────────────────────────────────────

/** Trimmed POST input. */
function post(string $key, string $default = ''): string
{
    $raw = $_POST[$key] ?? $default;
    return is_string($raw) ? trim($raw) : $default;
}

/** Trimmed GET input. */
function query(string $key, string $default = ''): string
{
    $raw = $_GET[$key] ?? $default;
    return is_string($raw) ? trim($raw) : $default;
}

// ── Time ──────────────────────────────────────────────────────────────────────

/** Human-readable relative time ("3 hours ago"). Falls back to formatted date. */
function timeAgo(string $dateTimeStr): string
{
    $diff = time() - strtotime($dateTimeStr);
    if ($diff < 60)     return 'just now';
    if ($diff < 3600)   return floor($diff / 60) . ' min ago';
    if ($diff < 86400) {
        $h = (int) floor($diff / 3600);
        return $h . ' ' . ($h === 1 ? 'hour' : 'hours') . ' ago';
    }
    if ($diff < 604800) {
        $d = (int) floor($diff / 86400);
        return $d . ' ' . ($d === 1 ? 'day' : 'days') . ' ago';
    }
    return date('M d, Y', (int) strtotime($dateTimeStr));
}

/** Format a datetime string for display. */
function formatDate(string $dateTimeStr, string $format = 'M d, Y - h:i a'): string
{
    $ts = strtotime($dateTimeStr);
    return $ts ? date($format, $ts) : $dateTimeStr;
}

// ── Strings ───────────────────────────────────────────────────────────────────

/** Truncate a string to $length chars with an ellipsis. Unicode-safe. */
function excerpt(string $text, int $length = 240): string
{
    $text = trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
    if (mb_strlen($text) <= $length) return $text;
    return rtrim(mb_substr($text, 0, $length), " \t\n\r.,;:-") . '…';
}

/** Estimated reading time in minutes for a block of text (min 1). */
function readingTime(string $text): int
{
    $words = str_word_count(strip_tags($text));
    return max(1, (int) ceil($words / 220));
}

/** Extract initials from a first/last name pair. */
function initials(string $firstname, string $lastname): string
{
    $a = mb_strtoupper(mb_substr($firstname, 0, 1));
    $b = mb_strtoupper(mb_substr($lastname,  0, 1));
    return $a . $b;
}

// ── File uploads ──────────────────────────────────────────────────────────────

/**
 * Validate an uploaded image and move it into $destinationDir.
 * Returns the new filename on success, or throws.
 *
 * Robustness features:
 *   - Converts ini_size/form_size/etc. error codes into friendly messages.
 *   - Validates extension AND (when fileinfo is available) MIME type.
 *   - Auto-creates the destination directory and verifies it is writable.
 *
 * @throws InvalidArgumentException  user-visible (bad file, too big, wrong type)
 * @throws RuntimeException          server-side problem (permissions, disk)
 */
function handleImageUpload(array $file, string $destinationDir): string
{
    if (empty($file['name']) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        throw new InvalidArgumentException('No file was uploaded.');
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $messages = [
            UPLOAD_ERR_INI_SIZE   => 'Image exceeds the server upload size limit.',
            UPLOAD_ERR_FORM_SIZE  => 'Image exceeds the form upload size limit.',
            UPLOAD_ERR_PARTIAL    => 'Image was only partially uploaded. Please try again.',
            UPLOAD_ERR_NO_TMP_DIR => 'Server misconfiguration: missing temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Server misconfiguration: failed to write uploaded file.',
            UPLOAD_ERR_EXTENSION  => 'Upload was blocked by a server extension.',
        ];
        throw new InvalidArgumentException($messages[$file['error']] ?? 'Upload failed (error code ' . $file['error'] . ').');
    }

    if (!is_uploaded_file($file['tmp_name'])) {
        // Not a real HTTP upload — reject to avoid path traversal.
        throw new InvalidArgumentException('Invalid upload attempt.');
    }

    if ($file['size'] > MAX_UPLOAD_SIZE) {
        $mb = number_format(MAX_UPLOAD_SIZE / 1024 / 1024, 1);
        throw new InvalidArgumentException("Image is larger than {$mb} MB.");
    }

    // Extension check first — it's the definitive whitelist for allowed types.
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $extToMime = [
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'webp' => 'image/webp',
        'gif'  => 'image/gif',
    ];
    if (!isset($extToMime[$ext])) {
        throw new InvalidArgumentException('Only JPEG, PNG, WebP or GIF images are allowed.');
    }

    // MIME cross-check when possible. Some servers report octet-stream, which
    // we ignore (the extension whitelist above already gated the request).
    if (function_exists('mime_content_type')) {
        $detected = @mime_content_type($file['tmp_name']);
        if ($detected && $detected !== 'application/octet-stream' && !in_array($detected, ALLOWED_MIME, true)) {
            throw new InvalidArgumentException('Only JPEG, PNG, WebP or GIF images are allowed.');
        }
    }

    // Ensure destination directory is ready and writable.
    if (!is_dir($destinationDir) && !mkdir($destinationDir, 0755, true) && !is_dir($destinationDir)) {
        throw new RuntimeException('Could not create the upload directory: ' . $destinationDir);
    }
    if (!is_writable($destinationDir)) {
        throw new RuntimeException('Upload directory is not writable. Check permissions on: ' . $destinationDir);
    }

    $safeFilename = bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;
    $destination  = rtrim($destinationDir, '/') . '/' . $safeFilename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new RuntimeException('Could not save the uploaded file to: ' . $destinationDir);
    }

    @chmod($destination, 0644);
    return $safeFilename;
}

// ── CSRF ──────────────────────────────────────────────────────────────────────

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrfToken() . '">';
}

/** Verify POSTed token. On failure, clears the token and aborts with 403. */
function verifyCsrf(): void
{
    $submitted = $_POST['csrf_token'] ?? '';
    $expected  = $_SESSION['csrf_token'] ?? '';

    if ($expected === '' || !is_string($submitted) || !hash_equals($expected, $submitted)) {
        unset($_SESSION['csrf_token']);
        http_response_code(403);
        Auth::setFlash('error', 'Security check failed. Please try again.');
        redirect($_SERVER['HTTP_REFERER'] ?? url('/'));
    }
}

// ── Avatar rendering ──────────────────────────────────────────────────────────

/**
 * Return a resolved avatar URL, falling back to the default avatar if the
 * stored filename is missing or the file has been removed from disk.
 */
function avatar_url(?string $filename): string
{
    $filename = trim((string) $filename);
    if ($filename === '' || !is_file(UPLOAD_DIR . $filename)) {
        return upload_url('default-avatar.png');
    }
    return upload_url($filename);
}

/** Return a resolved thumbnail URL with fallback. */
function thumbnail_url(?string $filename): string
{
    $filename = trim((string) $filename);
    if ($filename === '' || !is_file(UPLOAD_DIR . $filename)) {
        return upload_url('default-thumbnail.png');
    }
    return upload_url($filename);
}
