SELECT 
    p.id AS project_id,
    p.name,
    p.description,
    COUNT(DISTINCT cd.id) AS chara_count,
    COUNT(DISTINCT up2.user_id) AS user_count
FROM user_projects up
INNER JOIN projects p 
    ON up.project_id = p.id
LEFT JOIN chara_data cd
    ON cd.project_id = p.id
LEFT JOIN user_projects up2
    ON up2.project_id = p.id
WHERE up.user_id = :user_id
GROUP BY p.id, p.name, p.description
ORDER BY p.created_at DESC;
