<?php

include('../../../lib/setup/init.inc.php');
include('class.uploader.php');

if($_FILES['files']['tmp_name']) {

    if (!is_dir('../../../' . $_POST['path'])) {
        mkdir('../../../' . $_POST['path'], 0777, true);
    }

    $uploader = new Uploader();
    $data = $uploader->upload($_FILES['files'], array(
        'limit' => null,
        //Maximum Limit of files. {null, Number}
        'maxSize' => null,
        //Maximum Size of files {null, Number(in MB's)}
        'extensions' => null,
        //Whitelist for file extension. {null, Array(ex: array('jpg', 'png'))}
        'required' => false,
        //Minimum one file is required for upload {Boolean}
        'uploadDir' => '../../../' . $_POST['path'],
        //Upload directory {String}
        'title' => $_POST['id_registro'],
        //New file name {null, String, Array} *please read documentation in README.md
        'removeFiles' => true,
        //Enable file exclusion {Boolean(extra for jQuery.filer), String($_POST field name containing json data with file names)}
        'replace' => false,
        //Replace the file if it already exists  {Boolean}
        'perms' => null,
        //Uploaded file permisions {null, Number}
        'onCheck' => null,
        //A callback function name to be called by checking a file for errors (must return an array) | ($file) | Callback
        'onError' => null,
        //A callback function name to be called if an error occured (must return an array) | ($errors, $file) | Callback
        'onSuccess' => null,
        //A callback function name to be called if all files were successfully uploaded | ($files, $metas) | Callback
        'onUpload' => null,
        //A callback function name to be called if all files were successfully uploaded (must return an array) | ($file) | Callback
        'onComplete' => null,
        //A callback function name to be called when upload is complete | ($file) | Callback
        'onRemove' => null
        //A callback function name to be called by removing files (must return an array) | ($removed_files) | Callback
    ));

    if ($data['isComplete']) {
        $files = $data['data'];
        $objeto = new $_POST['classe']();
        $id = $objeto->uploadImage($_POST['id_registro'], $files['metas'][0]['name'], $_POST['path']);
        exit(json_encode($id));
    }

    if ($data['hasErrors']) {
        $errors = $data['errors'];
        exit(json_encode($errors));
    }
}


exit;