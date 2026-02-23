SELECT *
FROM notifications
WHERE user_id = :user_id AND is_deleted = false
ORDER BY is_read ASC, created_at DESC;