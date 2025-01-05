<?php

if (strpos($_SERVER['REQUEST_URI'], 'download.php') !== false) {
    die("Access Denied");
}

require 'includes/dbconfig.php';
require 'includes/commonfunc.php';

class FileDownloader
{

    private $dbh;

    private $baseDir;


    public function __construct($dbh)
    {
        $this->dbh = $dbh;
        $this->baseDir = 'useruploads/files/';
        @set_time_limit(0);

    }//end __construct()


    public function download($fileName, $newFileName=null)
    {
        if (empty($fileName)) {
            die("Please specify file name for download.");
        }

        $fname = basename($fileName);
        $fname = rtrim($fname);
        $fpath = $this->baseDir.$fname;

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

    }//end download()


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

    }//end getMimeType()


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
        header("Content-Length: ".$fsize);

    }//end setHeaders()


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

    }//end outputFile()


    private function logDownload($fname)
    {
        $stmt = $this->dbh->prepare("INSERT INTO dl_log (filename, ip, time) VALUES (?, ?, ?)");
        $stmt->bindParam(1, $fname);
        $stmt->bindParam(2, $_SERVER['REMOTE_ADDR']);
        $stmt->bindParam(3, date("Y-m-d H:i:s"));
        $stmt->execute();

    }//end logDownload()


    private function updateDownloadCounter($fname)
    {
        $dlcount = query_single("SELECT downloads FROM dl_count WHERE filename = '".$fname."'");
        $date = date("Y-m-d H:i:s");
        if (!$dlcount) {
            $i = 1;
            $stmt = $this->dbh->prepare("INSERT INTO dl_count (filename, downloads, count_started, last_dl) VALUES (?, ?, ?, ?)");
            $stmt->bindParam(1, $fname);
            $stmt->bindParam(2, $i);
            $stmt->bindParam(3, $date);
            $stmt->bindParam(4, $date);
            $stmt->execute();
        } else {
            $stmt = $this->dbh->prepare("UPDATE dl_count SET downloads = ? + 1, last_dl = ? WHERE filename = ?");
            $stmt->bindParam(1, $dlcount);
            $stmt->bindParam(2, $date);
            $stmt->bindParam(3, $fname);
            $stmt->execute();
        }

    }//end updateDownloadCounter()


}//end class

$dbh = new PDO(PDO_DB_DRIVER.':'.PDO_DB_FOLDER.'/'.PDO_DB_NAME);
$downloader = new FileDownloader($dbh);
$downloader->download($_GET['f'], $_GET['fc'] ?? null);
