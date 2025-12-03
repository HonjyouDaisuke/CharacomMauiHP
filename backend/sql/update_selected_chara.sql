UPDATE chara_data
SET
    is_selected = :is_selected,
    updated_by = :updated_by
WHERE
    id = :id;
