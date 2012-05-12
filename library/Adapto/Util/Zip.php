<?php
/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 * @subpackage utils
 *
 * @author Guido van Biemen <guido@ibuildings.nl>
 *
 * @copyright (c) 2007 Ibuildings.nl BV
 * @license see doc/LICENSE
 *

 */

/**
 * Some constants
 * @access private
 */
define("ATKZIP_ZIP", 0);
define("ATKZIP_UNZIP", 1);

/**
 * This class provide functions to extract from and add to ZIP archives
 *
 * @author Guido van Biemen <guido@ibuildings.nl>
 * @package adapto
 * @subpackage utils
 *
 */
class Adapto_Util_Zip
{
    public $m_zip_bin = ""; // defaulted to public
    public $m_unzip_bin = ""; // defaulted to public
    public $m_zipmode = "auto"; // can be auto, internal or infozip // defaulted to public
    public $m_testok = false; // defaulted to public

    /**
     * Constructor
     *
     * @return Adapto_Util_Zip
     */

    public function __construct()
    {
        $this->m_zipmode = Adapto_Config::getGlobal("zipmode", "auto");
        $this->m_zip_bin = Adapto_Config::getGlobal("ziplocation", "zip");
        $this->m_unzip_bin = Adapto_Config::getGlobal("unziplocation", "unzip");
        if (!$this->test())
            throw new Adapto_Exception("Adapto_Util_Zip: Error while testing");
    }

    /**
     * Get the (un)zip command
     *
     * @param int $type The type of zip (Adapto_ZIP or Adapto_UNZIP)
     * @param string $params The parameters for zippping or unzipping
     * @return string The command to execute
     */
    function getInfozipCommand($type, $params)
    {
        if ($type == ATKZIP_ZIP)
            $command = $this->m_zip_bin;
        if ($type == ATKZIP_UNZIP)
            $command = $this->m_unzip_bin;
        if ($params !== "")
            $command .= " " . $params;
        return $command;
    }

    /**
     * Run the (un)zip command
     *
     * @param int $type The type of zip (Adapto_ZIP or Adapto_UNZIP)
     * @param string $params The parameters for zipping or unzipping
     * @return string The return code
     */
    function runInfozipCommand($type, $params)
    {
        $command = $this->getInfozipCommand($type, $params);
        $output = array();
        Adapto_Util_Debugger::debug("Adapto_Util_Zip->runInfozipCommand: Executing command: $command");
        $returncode = NULL; //var for catching returncode fro exec.
        exec($command, $output, $returncode);
        Adapto_Util_Debugger::debug("Adapto_Util_Zip->runInfozipCommand: Return code was: " . $returncode);
        Adapto_var_dump($output, "Adapto_Util_Zip->runInfozipCommand: Console output");
        return $returncode;
    }

    /**
     * Get the error message based on the errorcode
     *
     * @param int $type The type of zip (Adapto_ZIP or Adapto_UNZIP)
     * @param int $errorcode The errorcode
     * @return string The errormessage
     */
    function getInfozipError($type, $errorcode)
    {
        if ($type == ATKZIP_UNZIP) {
            $codes = array(
                    0 => "Normal; no errors or warnings detected. (There may still be errors in the archive, but if so, they weren't particularly relevant to UnZip's processing and are presumably quite minor.)",
                    1 => "One or more warning errors were encountered, but processing completed successfully anyway. This includes zipfiles where one or more files was skipped due to unsupported compression method or encryption with an unknown password.",
                    2 => "A generic error in the zipfile format was detected. Processing may have completed successfully anyway; some broken zipfiles created by other archivers have simple work-arounds.",
                    3 => "A severe error in the zipfile format was detected. Processing probably failed immediately.",
                    4 => "UnZip was unable to allocate memory for one or more buffers during program initialization.",
                    5 => "UnZip was unable to allocate memory or unable to obtain a tty (terminal) to read the decryption password(s).",
                    6 => "UnZip was unable to allocate memory during decompression to disk.",
                    7 => "UnZip was unable to allocate memory during in-memory decompression.", 9 => "The specified zipfile(s) was not found.",
                    10 => "Invalid options were specified on the command line.", 11 => "No matching files were found.",
                    50 => "The disk is (or was) full during extraction.", 51 => "The end of the ZIP archive was encountered prematurely.",
                    80 => "The user aborted UnZip prematurely with control-C (or similar)",
                    81 => "Testing or extraction of one or more files failed due to unsupported compression methods or unsupported decryption.",
                    82 => "No files were found due to bad decryption password(s). (If even one file is successfully processed, however, the exit status is 1.)",);
        } elseif ($type == ATKZIP_ZIP) {
            $codes = array(0 => "Normal; no errors or warnings detected.",
                    2 => "The zipfile is either truncated or damaged in some way (e.g., bogus internal offsets) that makes it appear to be truncated.",
                    3 => "The structure of the zipfile is invalid; for example, it may have been corrupted by a text-mode (\"ASCII\") transfer.",
                    4 => "Zip was unable to allocate sufficient memory to complete the command.",
                    5 => "Internal logic error. (This should never happen; it indicates a programming error of some sort.)",
                    6 => "ZipSplit was unable to create an archive of the specified size because the compressed size of a single included file is larger than the requested size. (Note that Zip and ZipSplit still do not support the creation of PKWARE-style multi-part archives.)",
                    7 => "The format of a zipfile comment was invalid.",
                    8 => "Testing (-T option) failed due to errors in the archive, insufficient memory to spawn UnZip, or inability to find UnZip.",
                    9 => "Zip was interrupted by user (or superuser) action.", 10 => "Zip encountered an error creating or using a temporary file.",
                    11 => "Reading or seeking (jumping) within an input file failed.", 12 => "There was nothing for Zip to do (e.g., \"zip foo.zip\").",
                    13 => "The zipfile was missing or empty (typically when updating or freshening).",
                    14 => "Zip encountered an error writing to an output file (typically the archive); for example, the disk may be full.",
                    15 => "Zip could not open an output file (typically the archive) for writing.",
                    16 => "The command-line parameters were specified incorrectly.",
                    18 => "Zip could not open a specified file for reading; either it doesn't exist or the user running Zip doesn't have permission to read it.",);
        }
        return atkArrayNvl($codes, $errorcode, "Unknown error code returned by Infozip");
    }

    /**
     * Test if we can zip/unzip
     *
     * @return bool True if test was successfull or false if not
     */
    function test()
    {
        if ($this->m_testok)
            return true;

        Adapto_Util_Debugger::debug("Adapto_Util_Zip->test: Testing systems zip abilities");
        Adapto_Util_Debugger::debug("Adapto_Util_Zip->test: Zipmode = " . $this->m_zipmode);

        // If the php version is 5.2 or newer and the zip extension is loaded, we can use the
        // ziparchive class
        if (in_array($this->m_zipmode, array("auto", "internal"))) {
            Adapto_Util_Debugger::debug("Adapto_Util_Zip->test: Testing for php 5.2 and zip extension");
            $phpversion = phpversion();
            $zipextensionloaded = @extension_loaded("zip");
            Adapto_Util_Debugger::debug("Adapto_Util_Zip->test: PHP Version = " . $phpversion);
            Adapto_Util_Debugger::debug("Adapto_Util_Zip->test: extension_loaded('zip') = " . ($zipextensionloaded ? "true" : "false"));
            if (version_compare($phpversion, '5.2') > 0 && $zipextensionloaded) {
                Adapto_Util_Debugger::debug("Adapto_Util_Zip->test: PHP 5.2 or newer and the ZIP extension are present, TEST SUCCESFULL!");
                if ($this->m_zipmode == "auto")
                    $this->m_zipmode = "internal";
                $this->m_testok = true;
                return true;
            }
        }

        // If previous condition wasn't met, we can test the availability of the zip and unzip
        // commands
        if (in_array($this->m_zipmode, array("auto", "infozip"))) {
            $zipoutput = shell_exec($this->getInfozipCommand(ATKZIP_ZIP, "-h"));
            $unzipoutput = shell_exec($this->getInfozipCommand(ATKZIP_UNZIP, "-h"));
            Adapto_Util_Debugger::debug("Adapto_Util_Zip->test: php 5.2 or zip extension not found, now testing for infozip binaries");
            if ((strlen($zipoutput) > 0) && (strlen($unzipoutput) > 0)) {
                Adapto_Util_Debugger::debug("Adapto_Util_Zip->test: zip and unzip command responded, TEST SUCCESFULL!");
                if ($this->m_zipmode == "auto")
                    $this->m_zipmode = "infozip";
                $this->m_testok = true;
                return true;
            }
        }

        Adapto_Util_Debugger::debug("Adapto_Util_Zip->test: This system has no zip abilities, TEST FAILED!");
        Adapto_Util_Debugger::debug("Adapto_Util_Zip->test: Try upgrading to PHP 5.2 and installing the php zip extension");
        return false;
    }

    /**
     * Extract a zipfile
     *
     * @param string $archive The zip file
     * @param string $destination The destination folder
     * @param array $entries The entries
     * @return bool True if extract went successfull or false if not
     */
    function extract($archive, $destination, $entries = null)
    {
        if (!$this->test())
            throw new Adapto_Exception("Adapto_Util_Zip->extract: Could not extract, system is not capable of extracting from a ZIP archive");

        if ($this->m_zipmode == "internal") {
            $zip = new ZipArchive;
            if ($zip->open($archive) === TRUE) {
                if ($entries === NULL) {
                    $zip->extractTo($destination);
                } else {
                    $zip->extractTo($destination, $entries);
                }
                $zip->close();
                return true;
            } else {
                throw new Adapto_Exception("Adapto_Util_Zip->extract: Error while opening the zip archive ($archive)");
                return false;
            }
        }

        if ($this->m_zipmode == "infozip") {
            $entriesstring = is_array($entries) ? implode(" ", $entries) : $entries;
            $params = "'$archive' $entriesstring -d '$destination'";
            $returncode = $this->runInfozipCommand(ATKZIP_UNZIP, $params);
            if ($returncode <= 0) {
                return true;
            } else {
                throw new Adapto_Exception(
                        sprintf("Adapto_Util_Zip->extract: Infozip returned an error: %s (return code %d)", $this->getInfozipError(ATKZIP_UNZIP, $returncode),
                                $returncode));
                return false;
            }
        }

        return false;
    }

    /**
     * Add file to archive with optional filepath
     *
     * @param string $archive archive-path
     * @param string $filename file to add
     * @param string $filepath path where file will be placed in (optional, and only for zipmode "internal")
     * @return boolean $result
     */
    function add($archive, $filename, $filepath = "")
    {
        if (!$this->test())
            throw new Adapto_Exception("Adapto_Util_Zip->add: Could not add, system is not capable of add to a ZIP archive");

        if ($this->m_zipmode == "internal") {
            $zip = new ZipArchive;
            if ($zip->open($archive) === TRUE) {
                Adapto_Util_Debugger::debug("AtkZip::add|adding " . $filepath . basename($filename) . " to $archive");
                $zip->addFile($filename, $filepath . basename($filename));
                $zip->close();
                return true;
            } else {
                throw new Adapto_Exception("Adapto_Util_Zip->add: Error while opening the zip archive ($archive)");
                return false;
            }
        }

        if ($this->m_zipmode == "infozip") {
            $params = " -j $archive $filename";
            $returncode = $this->runInfozipCommand(ATKZIP_ZIP, $params);
            if ($returncode <= 0) {
                return true;
            } else {
                throw new Adapto_Exception(
                        sprintf("Adapto_Util_Zip->add: Infozip returned an error: %s (return code %d)", $this->getInfozipError(ATKZIP_ZIP, $returncode),
                                $returncode));
                return false;
            }
        }

        return false;
    }

}

?>