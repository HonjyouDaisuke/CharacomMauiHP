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
    foreach ($items['data']['entries'] as $folderItem) {
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
        is_selected: false
      );
      
      $success = $this->repo->insert($charaData);
      if ($success)
      {
        $msg .= "[success]".$folderItem['name']."\n";
      }
    }
    
    return [
      'success' => true,
      'message' => $msg,
    ];
  }
}