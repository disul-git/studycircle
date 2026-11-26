<?php
declare(strict_types=1);

function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function redirect(string $path): never {
    header('Location: ' . $path);
    exit;
}
function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}
function require_login(): void {
    if (!current_user()) {
        set_flash('error', 'Please log in to continue.');
        redirect('login.php');
    }
}
function require_admin(): void {
    require_login();
    if ((current_user()['role'] ?? 'student') !== 'admin') {
        http_response_code(403);
        exit('403 — You do not have permission to access this page.');
    }
}
function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}
function get_flash(): ?array {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function verify_csrf(): void {
    $sent = $_POST['csrf_token'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(419);
        exit('Your session token expired. Please go back, refresh the page, and try again.');
    }
}
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}
function is_post(): bool {
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}
function valid_id(mixed $id): int {
    return filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 0;
}
function excerpt(string $text, int $limit = 140): string {
    $text = trim(preg_replace('/\s+/', ' ', strip_tags($text)));
    return mb_strlen($text) > $limit ? mb_substr($text, 0, $limit - 1) . '…' : $text;
}
function badge(string $status): string {
    $class = match ($status) {
        'approved', 'active', 'visible' => 'success',
        'pending' => 'warning',
        'rejected', 'blocked', 'hidden' => 'danger',
        default => 'muted',
    };
    return '<span class="badge badge-' . $class . '">' . e(ucfirst($status)) . '</span>';
}
function user_is_admin(): bool {
    return (current_user()['role'] ?? '') === 'admin';
}
function posted_string(string $key, int $max = 255): string {
    $value = trim((string)($_POST[$key] ?? ''));
    return mb_substr($value, 0, $max);
}
function owned_resource(PDO $pdo, int $id, int $userId): ?array {
    $stmt = $pdo->prepare('SELECT * FROM resources WHERE id = ? AND uploader_id = ?');
    $stmt->execute([$id, $userId]);
    return $stmt->fetch() ?: null;
}
