<?php

namespace Backend\Domain\Service;

use Backend\Domain\Entities\FileInformation;

class FileNameService
{
    public static function getExtension(string $fileName): ?string
    {
        return pathinfo($fileName, PATHINFO_EXTENSION);
    }

    public static function getDataInfo(string $fileName): ?FileInformation
    {
        // 正規表現と一致させる
        if (preg_match('/^(.*?)_(.*?)-(.*?)\.(jpg|jpeg)$/i', $fileName, $matches)) {

            $charaName = $matches[1] ?? '';
            $materialName = $matches[2] ?? '';
            $timesName = $matches[3] ?? '';

            // 空チェック
            if ($charaName === '' || $materialName === '' || $timesName === '') {
                return null;
            }

            // FileInformationを返す
            return new FileInformation($charaName, $materialName, $timesName);

        } else {
            error_log("ファイル名の形式が正しくありません: ".$fileName);
            return null;
        }
    }
}
