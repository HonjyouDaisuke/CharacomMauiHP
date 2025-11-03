DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id CHAR(255) NOT NULL PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(255) UNIQUE,
    picture_url VARCHAR(512),
    box_user_id VARCHAR(255),
    box_access_token TEXT,
    box_refresh_token TEXT,
    token_expires_at DATETIME,
    role_id VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
