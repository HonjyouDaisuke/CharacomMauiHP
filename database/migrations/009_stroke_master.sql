DROP TABLE IF EXISTS stroke_master;

CREATE TABLE stroke_master (
    id VARCHAR(255) NOT NULL PRIMARY KEY DEFAULT(UUID()),
    chara_name VARCHAR(255),
    file_id VARCHAR(512),
    created_by char(36),
    updated_by CHAR(36),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);