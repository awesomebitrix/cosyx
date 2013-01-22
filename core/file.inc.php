<?php
/**
 * Cosix Bitrix Extender
 *
 * @package core
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 * @package core
 */
class CSX_File
{
	const CACHE_CONTENTS = 'xfile_cache_contents';

	public static function getContents($path, $forceNoCache = false, $compression = 0)
	{
		if ($forceNoCache) {
			return file_get_contents($path);
		}
		$key = 'file_' . md5($path);
		$store = PSP_Cache::getStore(self::CACHE_CONTENTS);
		if (($mtime = @filemtime($path)) > 0) {
			if (($hash = $store->get($key)) && ($hash[0] === $mtime)) {
				PSP_Debug::log('Loading file [' . $path . '] contents from cache');
				return $hash[1];
			}
			$result = file_get_contents($path);
			$store->set($key, array($mtime, $result), $compression);
			return $result;
		}
		else {
			$store->delete($key);
		}
		return false;
	}

	public static function isImage($file)
	{
		$pinfo = pathinfo($file);
		return isset($pinfo['extension']) && in_array(strtolower($pinfo['extension']), array(
			'gif', 'jpg', 'jpeg', 'png', 'swf', 'psd', 'bmp', 'tiff',
			'tiff', 'jpc', 'jp2', 'jpx', 'jb2', 'swc', 'iff', 'wbmp',
			'xbm', 'gen'
		));
	}

	public static function isVideo($file)
	{
		$pinfo = pathinfo($file);
		if (array_key_exists('extension', $pinfo)) {
			return in_array(strtolower($pinfo['extension']), array(
				// Movie, video, multimedia files
				'3gp', '3mm', 'avi', 'avs', 'bay', 'bik', 'dvr-ms',
				'flc', 'flv', 'ifo', 'ivf', 'm4v', 'mov', 'mp2v',
				'mp4', 'mpcpl', 'mpeg', 'mpg', 'qt', 'ratDVD', 'rm',
				'vob', 'wm', 'wmv', 'xvid',
			));
		}
		else {
			return false;
		}
	}

	public static function isFlash($file)
	{
		$pinfo = pathinfo($file);
		if (array_key_exists('extension', $pinfo)) {
			return in_array(strtolower($pinfo['extension']), array(
				'swf'
			));
		}
		else {
			return false;
		}
	}

	public static function isAudio($file)
	{
		$pinfo = pathinfo($file);
		if (array_key_exists('extension', $pinfo)) {
			return in_array(strtolower($pinfo['extension']), array(
				// Audio and music files
				'aac', 'aif', 'aifc', 'aiff', 'aifr', 'amr', 'ape',
				'asf', 'au', 'aud', 'aup', 'bwf', 'cda', 'dct', 'dss',
				'dts', 'dvf', 'esu', 'eta', 'flac', 'gsm', 'm4a', 'm4p',
				'mdi', 'midi', 'mp2', 'mp2a', 'mp3', 'mpc', 'mpega',
				'msv', 'nwc', 'nwp', 'ogg', 'psb', 'psm', 'ra', 'ram',
				'rel', 'sab', 'shn', 'smf', 'snd', 'speex', 'tta', 'vox',
				'vy3', 'wav', 'wave', 'wma', 'wpk', 'wpl', 'wv', 'wvc',
			));
		}
		else {
			return false;
		}
	}

	public static function isMedia($file)
	{
		return self::isVideo($file) || self::isFlash($file) || self::isAudio($file);
	}

	public static function getType($file)
	{
		$pinfo = pathinfo($file);

		$type = 'unknown';
		if (self::isImage($file)) {
			$type = 'image';
		}
		else if (self::isVideo($file)) {
			$type = 'video';
		}
		else if (self::isAudio($file)) {
			$type = 'audio';
		}
		else if (self::isFlash($file)) {
			$type = 'flash';
		}

		return array(
			'type' => $type
		, 'ext' => array_key_exists('extension', $pinfo)
				? strtolower($pinfo['extension'])
				: ''
		);
	}

	/**
	 * Normalize the given absolute path.
	 *
	 * This includes:
	 *   - Remove redundant slashes after the drive spec.
	 *   - resolve all ./, .\, ../ and ..\ sequences.
	 *     / as the separator.
	 * @param string $path Path to normalize.
	 * @return string
	 */
	public static function normalize($path, $base = null)
	{
		$path = preg_replace('|[/\\\]+|', '/', trim($path));
		$base = preg_replace('|[/\\\]+|', '/', trim($base));
		if (!empty($base) && !preg_match('/^(\w+:)?\//i', $path)) {
			$path = $base . '/' . $path;
		}

		$elements = explode('/', $path);
		$prefix = array_shift($elements); // Remove windows drive letter or leading empty element
		$result = '';
		$pass = 0;
		$elements = array_reverse($elements);

		if ('' === $elements[0]) {
			array_shift($elements); // Remove slash at the end of path
		}
		foreach ($elements as $e) {
			if ($e == '.') {
				$e = '';
			}
			if ($e === '' && $result !== '') {
				continue;
			}
			elseif ($e == '..') {
				$pass++;
			}
			elseif ($pass) {
				$pass--;
			}
			else {
				$result = '/' . $e . $result;
			}
		}

		if ('' === $result) $result = '/';
		$result = $prefix . $result;
		return $result;
	}

	/**
	 * Enlist directories and files into array
	 * If $recursive is set to true, enlist recursive tree
	 *
	 * @param string $dir
	 * @param boolean $recursive
	 * @static
	 * @access public
	 * @return array
	 */
	public static function ls($dir, $recursive = false, $filemask = null, $ignore = array())
	{
		$output = array();

		if (is_dir($dir) && ($handle = opendir($dir))) {
			while (false !== ($readdir = readdir($handle))) {
				if ($readdir != '.' && $readdir != '..' && !in_array($readdir, $ignore)) {
					$path = $dir . '/' . $readdir;
					if ($recursive && is_dir($path) && strpos(substr($path, strrpos($path, '/')), ".") != 1) {
						$output = array_merge($output, self::ls($path, $recursive, $filemask, $ignore));
					}

					if (is_file($path)) {
						if ($filemask == null || ($filemask != null && preg_match($filemask, $readdir, $matches) > 0)) {
							$output[] = $dir . '/' . $readdir;
						}
					}
				}
			}
			closedir($handle);
		}

		return $output;
	}

	public static function lsdir($dir, $recursive = false, $ignore = array())
	{
		$output = array();

		if (is_dir($dir) && ($handle = opendir($dir))) {
			while (false !== ($readdir = readdir($handle))) {
				if ($readdir != '.' && $readdir != '..' && !in_array($readdir, $ignore)) {
					$path = $dir . '/' . $readdir;
					if ($recursive && is_dir($path) && strpos(substr($path, strrpos($path, '/')), ".") != 1) {
						$output = array_merge($output, self::lsdir($path, $recursive, $ignore));
					}

					if (is_dir($path)) {
						$output[] = $dir . '/' . $readdir;
					}
				}
			}
			closedir($handle);
		}

		return $output;
	}

	/**
	 * Copy a file, or recursively copy a folder and its contents
	 *
	 * @param       string   $source    Source path
	 * @param       string   $dest      Destination path
	 * @return      bool     Returns TRUE on success, FALSE on failure
	 */
	public static function copy($source, $dest)
	{
		//echo "$source, $dest\n";
		// Simple copy for a file
		if (is_file($source)) return copy($source, $dest);

		// Make destination directory
		if (!is_dir($dest)) self::mkdir($dest, true);

		// Loop through the folder
		$dir = dir($source);

		while (false !== $entry = $dir->read()) {
			// Skip pointers
			if ($entry == '.' || $entry == '..') continue;
			// Deep copy directories
			if ($dest !== "$source/$entry") self::copy("$source/$entry", "$dest/$entry");
		}
		// Clean up
		$dir->close();
		return true;
	}

	/**
	 * Create directory
	 * If $recursive is set to true, create all directory tree recursive
	 *
	 * @param string $path
	 * @param int $mode
	 * @param boolean $recursive
	 * @static
	 * @access public
	 * @return boolean
	 */
	public static function mkdir($path, $recursive = false, $mask = null)
	{
		$result = true;
		if (is_dir($path)) {
			if (null !== $mask) self::chmod_umask($path, $mask);
		}
		else {
			$parent = dirname($path);
			if (is_dir($parent)) {
				if (is_writable($parent)) {
					if (mkdir($path) && null !== $mask) {
						self::chmod_umask($path, $mask);
					}
				}
				else {
					throw new PSP_Exception('Directory [' . $parent . '] is not writable');
				}
			}
			elseif ($recursive) {
				self::mkdir($parent, true, $mask);
				self::mkdir($path, false, $mask);
			}
			else {
				throw new PSP_Exception('Directory [' . $parent . '] is not exists and this is non-recursive mkdir call');
			}
		}
	}

	/**
	 * Rename file
	 *
	 * @param string $path
	 * @static
	 * @access public
	 * @return boolean
	 */
	public static function rename($oldname, $newname)
	{
		set_error_handler(create_function('', ';'), E_ALL);
		$result = rename($oldname, $newname);
		restore_error_handler();
		if (!$result) {
			throw new PSP_Exception('Cannot rename [' . $oldname . '] into [' . $newname . ']');
		}
		else {
			return true;
		}
	}

	/**
	 * Remove file
	 *
	 * @param string $path
	 * @static
	 * @access public
	 * @return boolean
	 */
	public static function rm($path, $silent = false)
	{
		$result = false;
		if (is_file($path)) {
			$result = unlink($path);
		}
		else if (!$silent) {
			throw new PSP_Exception('File not found [' . $path . ']');
		}
		return $result;
	}

	/**
	 * Remove directory
	 * If $recursive is set to true, remove nonempty directory with all its content
	 *
	 * @param string $path
	 * @param boolean $recursive
	 * @static
	 * @access public
	 * @return boolean
	 */
	public static function rmdir($path, $recursive = false)
	{
		$result = false;
		if (is_dir($path)) {
			if ($recursive) {
				$h = opendir($path);
				while (false !== ($file = readdir($h))) {
					if ('.' !== $file && '..' !== $file) {
						if (is_dir($path . '/' . $file)) {
							self::rmdir($path . '/' . $file, $recursive);
						}
						else {
							self::rm($path . '/' . $file);
						}
					}
				}
				closedir($h);
			}
			$result = rmdir($path);
		}
		return $result;
	}

	/**
	 * Clear directory
	 *
	 */
	public static function cleardir($path)
	{
		$result = false;
		if (is_dir($path)) {
			$h = opendir($path);
			while (false !== ($file = readdir($h))) {
				if ('.' !== $file && '..' !== $file) {
					if (is_dir($path . '/' . $file)) {
						self::rmdir($path . '/' . $file, true);
					}
					else {
						self::rm($path . '/' . $file);
					}
				}
			}
			closedir($h);
		}
		return $result;
	}

	public static function chmod($path, $mask, $recursive = false)
	{
		if (is_dir($path)) {
			if ($recursive) {
				$h = opendir($path);
				while (false !== ($file = readdir($h))) {
					if ('.' !== $file && '..' !== $file) {
						if (is_dir($path . '/' . $file)) {
							self::chmod($path . '/' . $file, $mask, $recursive);
						}
						else {
							self::chmod($path . '/' . $file, $mask);
						}
					}
				}
				closedir($h);
			}
		}
		self::chmod_umask($path, $mask);
	}

	public static function chmod_umask($path, $mask)
	{
		$m = umask(0);
		chmod($path, $mask);
		umask($m);
	}

	public static function getTmpDir()
	{
		return defined('PSP_TEMP_DIR') ? PSP_TEMP_DIR : sys_get_temp_dir();
	}

	public static function basename($path)
	{
		$path = explode('/', self::normalize($path));
		return $path[count($path) - 1];
	}

	public static function dirDiff($path1, $path2)
	{
		$path1 = explode("/", self::normalize($path1));
		$path2 = explode("/", self::normalize($path2));
		$path1_copy = $path1;
		$path2_copy = $path2;
		foreach ($path1_copy as $k => $p) {
			if (!isset($path2_copy[$k]) || $path1_copy[$k] != $path2_copy[$k]) break;
			array_shift($path1);
			array_shift($path2);
		}
		;
		return str_repeat("../", count($path2)) . implode("/", $path1);
	}

	public static function dirname($path)
	{
		$parts = pathinfo($path);
		return array_key_exists('dirname', $parts) ? $parts['dirname'] : '';
	}

	public static function untar($file, $dest)
	{
		$f = fopen($file, 'r');

		if ($f) {
			while (!feof($f)) {
				$line = fgets($f);
				if ($line !== FALSE) {
					$info = explode("\t", $line);

					$path = $dest . '/' . $info[0];
					$size = intval($info[1]);

					$content = $size > 0 ? fread($f, $size) : '';

					self::mkdir(dirname($path), true);
					file_put_contents($path, $content);
				}
			}

			fclose($f);
		}
	}

	public static function tar($dir, $tarFile, $basedir = null, $exclude = array('.svn'), $exclude_by_file = '.ignore')
	{
		$info = pathinfo($dir);

		if ($basedir == null) {
			$basedir = $dir;
		}

		$children = dir($dir);
		while (false !== $entry = $children->read()) {
			if ($entry == '.' || $entry == '..'
				|| in_array($entry, $exclude)
			) continue;

			$entry = $dir . '/' . $entry;

			if (is_file($entry)) {
				self::tarFile($entry, $tarFile, $basedir);
			}
			else if (is_dir($entry)) {
				if ($exclude_by_file == null || ($exclude_by_file != null && !file_exists($entry . '/' . $exclude_by_file))) {
					self::tar($entry, $tarFile, $basedir, $exclude, $exclude_by_file);
				}
			}
		}
		$children->close();
	}

	public static function tarFile($path, $tarFile, $basedir = null)
	{
		$out = fopen($tarFile, 'a+');

		self::tarFileEx($path, $out, $basedir);

		fclose($out);
	}

	public static function tarFileEx($path, $tarHandle, $basedir = null)
	{
		$in = fopen($path, 'r');

		fputs($tarHandle, $basedir != null ? PSP_File::dirDiff(PSP_File::normalize($path), $basedir) : $path);
		fputs($tarHandle, "\t" . filesize($path) . "\n");

		while (($buf = fread($in, 8192)) != '') {
			fwrite($tarHandle, $buf);
		}

		fclose($in);
	}

	public static function getModeOct($permissions)
	{
		$mode = 0;

		if ($permissions[1] == 'r') $mode += 0400;
		if ($permissions[2] == 'w') $mode += 0200;
		if ($permissions[3] == 'x') $mode += 0100;
		else if ($permissions[3] == 's') $mode += 04100;
		else if ($permissions[3] == 'S') $mode += 04000;

		if ($permissions[4] == 'r') $mode += 040;
		if ($permissions[5] == 'w') $mode += 020;
		if ($permissions[6] == 'x') $mode += 010;
		else if ($permissions[6] == 's') $mode += 02010;
		else if ($permissions[6] == 'S') $mode += 02000;

		if ($permissions[7] == 'r') $mode += 04;
		if ($permissions[8] == 'w') $mode += 02;
		if ($permissions[9] == 'x') $mode += 01;
		else if ($permissions[9] == 't') $mode += 01001;
		else if ($permissions[9] == 'T') $mode += 01000;

		return $mode;
	}

	public static function fopen1251($file)
	{
		$handle = fopen('php://memory', 'w+');
		if ($handle) {
			fwrite($handle, iconv('CP1251', 'UTF-8', file_get_contents($file)));
			rewind($handle);
		}

		return $handle;
	}

	public static function file1251($file)
	{
		$handle = fopen('php://memory', 'w+');

		if ($handle) {
			fwrite($handle, iconv('CP1251', 'UTF-8', file_get_contents($file)));
			rewind($handle);
			$contents = stream_get_contents($handle);
			return explode("\n", $contents);
		}

		return null;
	}

	public static function sizeToNum($v)
	{
		$l = substr($v, -1);
		$ret = substr($v, 0, -1);
		switch (strtoupper($l)) {
			case 'P':
				$ret *= 1024;
			case 'T':
				$ret *= 1024;
			case 'G':
				$ret *= 1024;
			case 'M':
				$ret *= 1024;
			case 'K':
				$ret *= 1024;
				break;
		}
		return $ret;
	}
}