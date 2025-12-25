UPDATE users
SET
    box_user_id = :box_user_id,
    box_access_token = :box_access_token,
    box_refresh_token = :box_refresh_token,
    token_expires_at = :token_expires_at,
    role_id = :role_id
WHERE
    id = :id;