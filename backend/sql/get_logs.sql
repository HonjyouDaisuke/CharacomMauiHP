SELECT
    id,
    user_id,
    level,
    screen,
    action,
    message,
    data,
    created_at
FROM app_logs
WHERE 1=1
--FROM_CONDITION--
--TO_CONDITION--
ORDER BY created_at DESC, id DESC
LIMIT :limit OFFSET :offset;
