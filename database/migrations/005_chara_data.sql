DROP TABLE IF EXISTS chara_data;

CREATE TABLE chara_data (
    id CHAR(36) NOT NULL PRIMARY KEY DEFAULT (UUID()),
    project_id CHAR(36),
    file_id VARCHAR(30),
    material_name TEXT,
    chara_name VARCHAR(5),
    times_name VARCHAR(20),
    is_selected BOOLEAN,
    created_by char(36),
    updated_by CHAR(36),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);