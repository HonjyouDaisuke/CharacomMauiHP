UPDATE users
SET
    role_id = :role_id
WHERE
    id = :id;