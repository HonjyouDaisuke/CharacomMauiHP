DROP TABLE IF EXISTS global_setting;

CREATE TABLE global_setting (
    id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
    top_folder_id VARCHAR(30),
    standard_folder_id VARCHAR(30),
    stroke_folder_id VARCHAR(30),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);