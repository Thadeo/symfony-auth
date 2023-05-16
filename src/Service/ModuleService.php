<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use ZipArchive;

class ModuleService
{
    private $modulePath;
    private $uploadFileLimit;

    public function __construct(
        string $modulePath,
        string $uploadFileLimit
        )
    {
        $this->modulePath = $modulePath;
        $this->uploadFileLimit = $uploadFileLimit;
    }

    /**
     * Upload Module File
     * 
     * Zip file
     * 
     * @param Request request
     * @return array
     */
    public function uploadModuleFile(
        Request $request)
    {
        try {

            // Hold File System
            $filesystem = new Filesystem();
            
            // Upload File
            $uploadedFile = $request->files->get('file');

            // Verify upload file
            if(empty($uploadedFile)) throw new \Exception("module file is required");
            
            // Module Name
            $moduleName = str_replace('.'.$uploadedFile->getClientOriginalExtension(), '', $uploadedFile->getClientOriginalName());
            
            // Hold Directory
            $moduleDir = $this->modulePath . $moduleName;

            // Check if the file is a valid zip file
            $fileExtension = $uploadedFile->getClientOriginalExtension();
            
            // Verify file extension
            if(!in_array($fileExtension, ['zip'])) throw new \Exception("Invalid file format. Please upload a zip file.");
            

            // Validate the uploaded file size
            $maxFileSize = $this->uploadFileLimit * 1024 * 1024;
            
            // Verify file limit
            if ($uploadedFile->getSize() > $maxFileSize) throw new \Exception("Invalid file size. Please upload a file up to ".$this->uploadFileLimit." in size.");
            

            // Check if the module already exists
            if (is_dir($moduleDir)) throw new \Exception("Module already exists.");
            
            // Create the module directory
            //$createModuleDr = $filesystem->mkdir($moduleDir);
            
            // Failed to create directory
            //if($createModuleDr instanceof IOExceptionInterface) throw new \Exception("Failed to create module directory.");
            
            // Move the uploaded file to the module directory
            $moveUploadedFiled = $uploadedFile->move($this->modulePath, $uploadedFile->getClientOriginalName());

            // Verify if moved
            if($moveUploadedFiled instanceof FileException) {
                
                // Remove module file
                $filesystem->remove($this->modulePath . '/' . $uploadedFile->getClientOriginalName());

                // Exception
                throw new \Exception("Failed to upload your module");
            }

            // Hold Extract the module file
            $zipFilePath = $this->modulePath . '/' . $uploadedFile->getClientOriginalName();
            
            // Prepaire zip command
            $zip = new ZipArchive();

            // Verify if file is unziped
            if ($zip->open($zipFilePath) === true) {
                
                // Verify if file is extract
                $zip->extractTo($this->modulePath);

                // Close zip command
                $zip->close();

            }else {

                // Close zip command
                $zip->close();

                // Remove module dr
                if(is_dir($moduleDir)) $filesystem->remove($moduleDir);

                // Remove module zip file
                $filesystem->remove($this->modulePath . '/' . $uploadedFile->getClientOriginalName());

                // Exception
                throw new \Exception("Failed to open the uploaded file as a valid zip file.");
            }

            // Remove module zip file
            $filesystem->remove($this->modulePath . '/' . $uploadedFile->getClientOriginalName());

            // Validate the module structure
            $moduleStructure = $this->validateModuleStructure($moduleDir, ['config'], ['__MACOSX']);

            // Exeption
            if($moduleStructure instanceof \Exception) {

                // Remove Dr
                $filesystem->remove($moduleDir);

                // Exception
                throw new \Exception($moduleStructure->getMessage());
            }

            // Return data
            return [ 'name' => $moduleName];

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Validate Module Structure
     * 
     * @param string moduleDr
     * @param array drStructure
     * @param array unsupportDrFile
     * @return bool
     */
    private function validateModuleStructure(
        string $moduleDir,
        array $drStructure,
        array $unsupportDrFile = null
    ) {
        try {

            // Remove unsupported data
            if($unsupportDrFile) $this->removeModuleFolderFileUnsupported($unsupportDrFile);

            // Validate Module Info file
            $moduleInfo = $this->validateModuleInfoFile($moduleDir);

            // Exception
            if($moduleInfo instanceof \Exception) throw new \Exception($moduleInfo->getMessage());

            // Hold file system
            $filesystem = new Filesystem();

            // Loop Dr item
            foreach ($drStructure as $dr) {

                // Hold new Module Dr
                $moduleDrPath = $moduleDir . '/' . $dr;

                // Verify Structure
                if (!$filesystem->exists($moduleDrPath) || !is_dir($moduleDrPath)) throw new \Exception("Invalid module structure");
            }

            // Return response
            return true;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Validate Module info file
     * 
     * @param string moduleDr
     * @return array
     */
    private function validateModuleInfoFile(
        string $moduleDir
    ) {
        try {

            // Validate the info.json file in module
            $moduleInfoJson = file_exists($moduleDir . '/info.json');

            // Verify info json file
            if(!$moduleInfoJson) throw new \Exception("Module info.json file not exist");

            // Module json info
            $moduleFile = $moduleDir . '/info.json';

            // Parse the json file
            $moduleInfo = json_decode(file_get_contents($moduleFile), true);

            // Verify parse
            if(empty($moduleInfo) || empty($moduleInfo['name']) || empty($moduleInfo['version']) || empty($moduleInfo['author']) || empty($moduleInfo['description'])) throw new \Exception("module info.json data missing");

            // Return response
            return $moduleInfo;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Remove Module Folder File
     * 
     * @param array data
     * @return bool
     */
    private function removeModuleFolderFileUnsupported(
        array $data
    ) {
        try {

            // Unset important folder/file
            unset($data['config']);
            unset($data['module.php']);

            // Loop Data
            foreach ($data as $key => $item) {
                
                // File to remove
                $removeDrFile = $this->modulePath . $item;
                
                // Verify if Dr exist
                if (is_dir($removeDrFile)) {
                    $files = glob($removeDrFile . '/*');
                    foreach ($files as $file) {
                        
                        // Change Permission
                        chmod($file, 0777);

                        // Remove File
                        if (is_file($file)) unlink($file);

                        // Remove Dr
                        if (is_dir($file)) rmdir($file);
                    }

                    // Change Permission
                    chmod($removeDrFile, 0777);

                    // Remove Folder
                    rmdir($removeDrFile);
                }

                // Verify if File exist
                if(is_file($removeDrFile)) {

                    // Change Permission
                    chmod($removeDrFile, 0777);

                    // Remove file
                    unlink($removeDrFile);
                }
            }
            

            // Return response
            return true;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * List Modules
     * 
     * @return array
     */
    public function listModules(): array
    {
        $modules = [];

        // Scan modules
        $scanModuleDr = scandir($this->modulePath);

        // Loop modules
        foreach ($scanModuleDr as $checkFile) {

            // Check file
            if ($checkFile !== '.' && $checkFile !== '..') {
                
                // Hold Module Path
                $modulePath = $this->modulePath . '/' . $checkFile;

                // Module json info
                $moduleFile = $modulePath . '/info.json';

                // Verify json file
                if (file_exists($moduleFile)) {

                    // Parse the json file
                    $moduleInfo = json_decode(file_get_contents($moduleFile), true);

                    // Loop module info
                    foreach ($moduleInfo as $key => $value) {
                        # info...
                        $module[$key] = $value;
                    }

                    // Add module info
                    $modules[] = $module;
                }
            }
        }

        // Return module
        return $modules;
    }
}
