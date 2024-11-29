<?php
require_once "Notification.php";

$notification = new Notification($db);
$unread_notifications = $notification->getUnreadNotifications($_SESSION['user_id']);
?>

<div class="notification-bar">
    <div class="notification-toggle">
        <i class="fas fa-bell"></i>
        <?php if(count($unread_notifications) > 0): ?>
            <span class="notification-count"><?php echo count($unread_notifications); ?></span>
        <?php endif; ?>
    </div>
    
    <div class="notification-dropdown">
        <?php if(empty($unread_notifications)): ?>
            <p class="no-notifications">No new notifications</p>
        <?php else: ?>
            <?php foreach($unread_notifications as $notif): ?>
                <div class="notification-item" data-id="<?php echo $notif['id']; ?>">
                    <p class="notification-message"><?php echo htmlspecialchars($notif['message']); ?></p>
                    <span class="notification-time">
                        <?php echo date('M j, Y g:i a', strtotime($notif['created_at'])); ?>
                    </span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div> 