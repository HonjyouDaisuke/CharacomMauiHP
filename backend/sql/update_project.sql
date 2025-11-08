UPDATE projects
SET
    name = :name,
    description = :description,
    folder_id = :folder_id,
    chara_folder_id = :chara_folder_id,
    created_by = :created_by
WHERE
    id = :id;