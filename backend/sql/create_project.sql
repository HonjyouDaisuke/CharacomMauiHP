INSERT INTO users 
  (
    name, 
    description, 
    folder_id, 
    chara_folder_id, 
    created_by
  ) 
VALUES 
  (
    :name,
    :description,
    :folder_id,
    :chara_folder_id,
    :created_by
  );
