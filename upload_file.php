 public function upload_file($it)
  {
      $fileSize = filesize($it);
      $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
      $filetype = finfo_file($fileinfo, $it);
      $file_error=[];
      if ($fileSize > 6145728) { // 3 MB (1 byte * 1024 * 1024 * 3 (for 3 MB))
          $file_error[]='error';
      }
      $allowedTypes = [ 
          'image/png' => 'png',
          'image/jpeg' => 'jpg' 
       ];
      if(!in_array($filetype, array_keys($allowedTypes))) {
          $file_error[]='error'; 
      }
      if(!count($file_error))
      {
          $filename =md5(date('Y-m-d h:i:s').rand(0,555555555555)); 
          $extension = $allowedTypes[$filetype];
          $targetDirectory ='../site/assets/uploads/'; 
          $newFilepath = $targetDirectory . "/" . $filename . "." . $extension;

          if (!copy($it, $newFilepath )) { // Copy the file, returns false if failed
              die("Can't move file.");
           }
           unlink($it);
           return $filename . "." . $extension;  
      }
      else return '';
  }
