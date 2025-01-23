<?php

namespace Ruubik\Service;

use Ruubik\Service\Db\Database;

class FileDownloader
{
    private $baseDir;

    public function __construct(private readonly Database $db, string $baseDir = 'useruploads/files/')
    {
        @set_time_limit(0);
    }

    public function download($fileName, $newFileName = null)
    {
        if (empty($fileName)) {
            die("Please specify file name for download.");
        }

        $fname = basename((string) $fileName);
        $fname = rtrim($fname);
        $fpath = $this->baseDir . $fname;

        if (!is_file($fpath)) {
            die("File does not exist. Make sure you specified correct file name.");
        }

        $fsize = filesize($fpath);
        $mtype = $this->getMimeType($fpath);

        if ($newFileName === null) {
            $asfname = $fname;
        } else {
            $asfname = str_replace(['"', "'", '\\', '/'], '', $newFileName);
            if ($asfname === '') {
                $asfname = 'NoName';
            }
        }

        $this->setHeaders($mtype, $asfname, $fsize);
        $this->outputFile($fpath);

        $this->logDownload($fname);
        $this->updateDownloadCounter($fname);
    }

    private function getMimeType($fpath)
    {
        $mtype = '';

        if (function_exists('mime_content_type')) {
            $mtype = mime_content_type($fpath);
        } else if (function_exists('finfo_file')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mtype = finfo_file($finfo, $fpath);
            finfo_close($finfo);
        }

        if ($mtype == '') {
            $mtype = "application/force-download";
        }

        return $mtype;
    }

    private function setHeaders($mtype, $asfname, $fsize)
    {
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Type: $mtype");
        header("Content-Disposition: attachment; filename=\"$asfname\"");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . $fsize);
    }

    private function outputFile($fpath)
    {
        $file = @fopen($fpath, "r");
        if ($file) {
            while (!feof($file)) {
                print(fread($file, (1024 * 8)));
                flush();
                if (connection_status() != 0) {
                    @fclose($file);
                    die();
                }
            }

            @fclose($file);
        }
    }

    private function logDownload($fname)
    {
        $this->db->execute(
            "INSERT INTO dl_log (filename, ip, time) VALUES (?, ?, ?)",
            [
                $fname,
                $_SERVER['REMOTE_ADDR'],
                date("Y-m-d H:i:s"),
            ]
        );
    }

    private function updateDownloadCounter($fname)
    {
        $dlcount = query_single("SELECT downloads FROM dl_count WHERE filename = '" . $fname . "'");
        $date = date("Y-m-d H:i:s");
        if (!$dlcount) {
              $this->db->execute(
                  "INSERT INTO dl_count (filename, downloads, count_started, last_dl) VALUES (?, ?, ?, ?)",
                  [
                      $fname,
                      1,
                      $date,
                      $date,
                  ]
              );
        } else {
            $this->db->execute(
                "UPDATE dl_count SET downloads = ? + 1, last_dl = ? WHERE filename = ?",
                [
                    $dlcount,
                    $date,
                    $fname,
                ]
            );
        }
    }
}
