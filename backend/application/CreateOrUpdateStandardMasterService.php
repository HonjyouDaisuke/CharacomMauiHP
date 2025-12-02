<?php
namespace Backend\Application;

use Backend\Domain\Entities\StandardMaster;
use Backend\Infrastructure\StandardMasterRepository;

class CreateOrUpdateStandardMasterService
{
  private StandardMasterRepository $repo;

  public function __construct(StandardMasterRepository $repo)
  {
    $this->repo = $repo;
  }

  public function execute(array $items, string $userId): array
  {
    $msg = "";
    // entries 分だけ1件ずつ呼び出す
    foreach ($items['data'] as $folderItem) {
      $charaName = pathinfo($folderItem['name'], PATHINFO_FILENAME);
      
      if ($charaName === null) {
        $msg .= "ファイル名変換に失敗しました。fileName=".$folderItem['name'].'\n';
        continue;
      }    
      $standardData = new StandardMaster(
        id: "",
        chara_name: $charaName,
        file_id: $folderItem['id'],
        created_by: $userId,
        updated_by: $userId
      );
      
      $id = $this->repo->isExists($standardData);

      // $idが存在しなかったら、新規なので追加(insert)
      if ($id === null )
      {
        $success = $this->repo->insert($standardData);
        if (!$success)
        {
          $msg .= "[failed]".$folderItem['name']."\n";
          continue;
        }
        $msg .= "[success]".$folderItem['name']."\n";
        continue;
      }
      
      // $idが存在したら、更新処理
      $success = $this->repo->update($standardData, $id);
      if (!$success)
      {
        $msg .= "[update failed]".$folderItem['name']."\n";
        continue;
      }
      $msg .= "[updated]".$folderItem['name']."\n";
        
    }
    
    return [
      'success' => true,
      'message' => $msg,
    ];
  }
}