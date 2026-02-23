UPDATE notifications
SET
    is_deleted = true
WHERE
    id = :id;
