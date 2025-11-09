INSERT INTO projects 
  (
    id,
    name, 
    description, 
    folder_id, 
    chara_folder_id, 
    created_by
  ) 
VALUES 
  (
    :id,
    :name,
    :description,
    :folder_id,
    :chara_folder_id,
    :created_by
  );
