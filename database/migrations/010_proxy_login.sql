DROP TABLE IF EXISTS proxy_login;
 
CREATE TABLE proxy_login (
    id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
 
    from_user_id CHAR(36) NOT NULL,
    from_name VARCHAR(100),
    from_email VARCHAR(255),
    from_picture_url VARCHAR(512),
    from_box_user_id VARCHAR(255),
    from_box_access_token TEXT,
    from_box_refresh_token TEXT,
    from_role_id CHAR(255),
 
    to_user_id CHAR(36) NOT NULL,
    to_name VARCHAR(100),
    to_email VARCHAR(255),
    to_picture_url VARCHAR(512),
    to_box_user_id VARCHAR(255),
    to_box_access_token TEXT,
    to_box_refresh_token TEXT,
    to_role_id CHAR(255),
 
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 
    UNIQUE KEY uq_proxy_login_from (from_user_id)
);
