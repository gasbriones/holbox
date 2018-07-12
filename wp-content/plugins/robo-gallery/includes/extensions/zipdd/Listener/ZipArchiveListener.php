<?php
/**
 *
 * @author Greg Kappatos
 *
 * This class serves as an observer/listener which can be implemented
 * by any other class who is interested in the PHPZip events.
 * Simply implement the methods and call Stream\ZipStream or
 * File\Zip::addListener($this) from inside your class.
 *
 */

namespace PHPZip\Zip\Listener;

	interface ZipArchiveListener {

	    public function onBuildZipEntry(array $params);

	    public function onOpenStream(array $params);

	    public function onAddFile(array $params);

	    public function onAddLargeFile(array $params);

	    public function onSendZip(array $params);

	    public function onException(array $params);

	}