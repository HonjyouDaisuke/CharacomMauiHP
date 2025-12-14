SELECT
    p.id AS project_id,
    p.name AS project_name,
    COALESCE(p.description, '') AS project_description,
    p.folder_id AS project_folder_id,
    p.chara_folder_id AS chara_folder_id,
    DATE_FORMAT(p.created_at, '%Y-%m-%dT%H:%i:%s') AS created_at,
    COALESCE(u_creator.name, '') AS created_by,
    DATE_FORMAT(p.updated_at, '%Y-%m-%dT%H:%i:%s') AS updated_at,

    -- CharaData 件数
    COUNT(DISTINCT cd.id) AS chara_count,

    -- 参加者一覧（カンマ区切り）
    GROUP_CONCAT(
        DISTINCT u_member.name
        ORDER BY u_member.name
        SEPARATOR ', '
    ) AS participants
FROM projects p

-- 作成者
LEFT JOIN users u_creator
    ON p.created_by = u_creator.id

-- CharaData
LEFT JOIN chara_data cd
    ON cd.project_id = p.id

-- プロジェクト参加者
LEFT JOIN user_projects up
    ON up.project_id = p.id
LEFT JOIN users u_member
    ON up.user_id = u_member.id

WHERE p.id = :project_id
GROUP BY
    p.id,
    p.name,
    p.description,
    p.folder_id,
    p.chara_folder_id,
    p.created_at,
    u_creator.name,
    p.updated_at;