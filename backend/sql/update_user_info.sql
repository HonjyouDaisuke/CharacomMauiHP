UPDATE users
SET
    name = :name,
    email = :email,
    picture_url = :picture_url
WHERE
    id = :id;