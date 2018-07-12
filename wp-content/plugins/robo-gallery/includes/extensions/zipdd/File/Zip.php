<?php
/**
 *
 * @author A. Grandt <php@grandt.com>
 * @author Greg Kappatos
 *
 * This class serves as a concrete zip file archive.
 *
 */

namespace PHPZip\Zip\File;

use com\grandt\BinStringStatic;
use PHPZip\Zip\Core\AbstractZipArchive;

	class Zip extends AbstractZipArchive {

	    const MEMORY_THRESHOLD = 1048576; // 1 MB - Auto create temp file if the zip data exceeds this
	    const STREAM_CHUNK_SIZE = 65536; // 64 KB

	    private $_zipData = null;
	    private $_zipFile = null;

	    public function __construct($useZipFile = false) {
	        parent::__construct(self::STREAM_CHUNK_SIZE);

	        if ($useZipFile) {
	            $this->_zipFile = tmpfile();
	        } else {
	            $this->_zipData = '';
	        }
	    }

	    public function __destruct() {
	        if (is_resource($this->_zipFile)) {
	            fclose($this->_zipFile);
	        }

	        $this->_zipData = null;
	    }

	    public function setZipFile($fileName) {
	        if (is_file($fileName)) {
	            unlink($fileName);
	        }

	        $fd = fopen($fileName, "x+b");

	        if (is_resource($this->_zipFile)) {
	            rewind($this->_zipFile);

	            while (!feof($this->_zipFile)) {
	                fwrite($fd, fread($this->_zipFile, $this->streamChunkSize));
	            }

	            fclose($this->_zipFile);
	        } else {
	            fwrite($fd, $this->_zipData);
	            $this->_zipData = null;
	        }

	        $this->_zipFile = $fd;
	        return true;
	    }

	    public function saveZipFile($fileName) {
	        return $this->setZipFile($fileName);
	    }

	    public function getZipFile() {
	        if (!$this->isFinalized) {
	            $this->finalize();
	        }

	        $this->zipFlush();
	        rewind($this->_zipFile);
	        return $this->_zipFile;
	    }

	    public function sendZip($fileName = null, $contentType = self::CONTENT_TYPE, $utf8FileName = null, $inline = false) {
	        if (!$this->isFinalized) {
	            $this->finalize();
	        }

	        if ($this->buildResponseHeader($fileName, $contentType, $utf8FileName, $inline)) {
	            return true;
	        }
	        return false;
	    }

	    public function getZipData() {
	        $result = null;

	        if (!$this->isFinalized) {
	            $this->finalize();
	        }

	        if (!is_resource($this->_zipFile)) {
	            $result = $this->_zipData;
	        } else {
	            rewind($this->_zipFile);
	            $stat   = fstat($this->_zipFile);
	            $result = fread($this->_zipFile, $stat['size']);
	        }

	        return $result;
	    }

	    public function getArchiveSize() {
	        if (!is_resource($this->_zipFile)) {
	            return BinStringStatic::_strlen($this->_zipData);
	        }

	        $stat = fstat($this->_zipFile);
	        return $stat['size'];
	    }

	    public function onBuildZipEntry(array $params) {
	        $this->zipWrite($params['zipEntry']);
	    }

	    public function onBeginAddFile(array $params) {
	        if (!is_resource($this->_zipFile) && ($this->offset + $params['gzLength']) > self::MEMORY_THRESHOLD) {
	            $this->zipFlush();
	        }
	    }

	    public function onEndAddFile(array $params) {
	        $this->zipWrite($params['gzData']);
	    }

	    public function onBeginBuildResponseHeader() {
	        if (!$this->isFinalized) {
	            $this->finalize();
	        }
	    }

	    public function onEndBuildResponseHeader() {
	        header('Connection: close');
	        header('Content-Length: ' . $this->getArchiveSize());

	        if (!is_resource($this->_zipFile)) {
	            echo $this->_zipData;
	        } else {
	            rewind($this->_zipFile);

	            while (!feof($this->_zipFile)) {
	                echo fread($this->_zipFile, $this->streamChunkSize);
	            }
	        }
	    }

	    public function onOpenStream() {
	        $this->zipFlush();
	    }

	    public function onProcessFile(array $params) {
	        $this->zipWrite($params['data']);
	    }

	    public function zipVerifyMemBuffer($gzLength) {
	        if (!is_resource($this->_zipFile) && ($this->offset + $gzLength) > self::MEMORY_THRESHOLD) {
	            $this->zipFlush();
	        }
	    }

	    public function zipWrite($data) {
	        if (!is_resource($this->_zipFile)) {
	            $this->_zipData .= $data;
	        } else {
	            fwrite($this->_zipFile, $data);
	            fflush($this->_zipFile);
	        }
	    }

	    public function zipFlush() {
	        if (!is_resource($this->_zipFile)) {
	            $this->_zipFile = tmpfile();
	            fwrite($this->_zipFile, $this->_zipData);
	            $this->_zipData = null;
	        }
	    }

	    public function zipFlushBuffer() {
	        // Does nothing.
	    }
	}
