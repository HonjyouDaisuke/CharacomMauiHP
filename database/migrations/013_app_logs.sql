DROP TABLE IF EXISTS app_logs;

CREATE TABLE app_logs (
    id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
    user_id CHAR(36) NOT NULL,
    level VARCHAR(20) NOT NULL,
    screen VARCHAR(200) NULL,
    action VARCHAR(200) NULL,

    message TEXT NULL,
    data JSON NULL,

    correlation_id VARCHAR(100) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created_at (created_at),
    INDEX idx_user_id (user_id),
    INDEX idx_level (level)
);
