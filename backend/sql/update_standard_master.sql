UPDATE standard_master
SET
    chara_name = :chara_name,
    file_id = :file_id,
    updated_by = :updated_by
WHERE
    id = :id;
