UPDATE notifications
SET
    is_read = true,
WHERE
    id = :id;
