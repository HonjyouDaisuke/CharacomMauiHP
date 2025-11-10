<?php
namespace Backend\Application;

use Backend\Domain\Entities\CharaData;
use Backend\Domain\Service\FileNameService;
use Backend\Infrastructure\CharaDataRepository;

class CreateOrUpdateCharaDataService
{
  private CharaDataRepository $repo;

  public function __construct(CharaDataRepository $repo)
  {
    $this->repo = $repo;
  }

  public function execute(array $items, string $projectId, string $userId): array
  {
    $msg = "";
    // entries 分だけ1件ずつ呼び出す
    foreach ($items['data'] as $folderItem) {
      $fileInfo = FileNameService::getDataInfo($folderItem['name']);
      
      if ($fileInfo === null) {
        $msg .= "ファイル名変換に失敗しました。fileName=".$folderItem['name'].'\n';
        continue;
      }    
      $charaData = new CharaData(
        id: "",
        project_id: $projectId,
        file_id: $folderItem['id'],
        material_name: $fileInfo->MaterialName,
        chara_name: $fileInfo->CharaName,
        times_name: $fileInfo->TimesName,
        created_by: $userId,
        updated_by: $userId,
        is_selected: false
      );
      
      $id = $this->repo->isExists($charaData);

      // $idが存在しなかったら、新規なので追加(insert)
      if ($id === null )
      {
        $success = $this->repo->insert($charaData);
        if (!$success)
        {
          $msg .= "[failed]".$folderItem['name']."\n";
          continue;
        }
        $msg .= "[success]".$folderItem['name']."\n";
        continue;
      }
      
      // $idが存在したら、更新処理
      $success = $this->repo->update($charaData, $id);
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