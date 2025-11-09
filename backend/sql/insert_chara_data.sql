INSERT INTO chara_data 
  (
    project_id, 
    file_id, 
    material_name, 
    chara_name,
    times_name, 
    created_by
  ) 
VALUES 
  (
    :project_id,
    :file_id,
    :material_name,
    :chara_name,
    :times_name,
    :created_by
  );
