UPDATE chara_data
SET
    material_name = :material_name,
    chara_name = :chara_name,
    times_name = :times_name,
    updated_by = :updated_by
WHERE
    id = :id;
