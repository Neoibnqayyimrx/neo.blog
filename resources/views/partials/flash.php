<?php
/** Flash partial — expects $flashes = ['success' => '...', 'error' => '...'] */
$flashes = $flashes ?? [];
foreach ($flashes as $type => $msg) {
    if (!$msg) continue;
    $class = match ($type) {
        'success' => 'alert--success',
        'error'   => 'alert--error',
        'warning' => 'alert--warning',
        default   => 'alert--info',
    };
    $icon = match ($type) {
        'success' => 'ri-checkbox-circle-line',
        'error'   => 'ri-error-warning-line',
        'warning' => 'ri-alert-line',
        default   => 'ri-information-line',
    };
    echo '<div class="alert ' . $class . '"><i class="' . $icon . '"></i><div>' . e($msg) . '</div></div>';
}
?>
