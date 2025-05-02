<?php

namespace KhanhDuy\DanhmuchanhchinhvnMaps\Helpers;

use Illuminate\Support\Facades\Log;

class DownloadFile
{
    const FILE_URL = "https://raw.githubusercontent.com/devcontainerDuy/danhmuchanhchinhvn-maps/refs/heads/main/storage/excel/gso.xls";
    public static function download()
    {
        try {
            if (!file_exists(storage_path('excel'))) {
                mkdir(storage_path('excel'), 0777, true);
            }

            $client = new \GuzzleHttp\Client([
                'verify' => false,
            ]);

            $response = $client->get(self::FILE_URL, [
                'sink' => storage_path('excel/gso.xls'),
            ]);

            return $response->getStatusCode() === 200 ? storage_path('excel/gso.xls') : null;
        } catch (\Exception $e) {
            Log::error("Download failed: " . $e->getMessage());
            return null;
        }
    }

}