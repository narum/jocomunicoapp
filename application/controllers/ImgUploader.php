<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class ImgUploader extends REST_Controller {

    public function __construct() {
        parent::__construct();
    }

    //MODIF: mirar que hacer aqui...
    public function index_get() {
        // CHECK COOKIES
        if (!$this->session->userdata('uname')) {
            redirect(base_url(), 'location');
        } else {
            if (!$this->session->userdata('cfguser')) {
                $this->BoardInterface->loadCFG($this->session->userdata('uname'));
                $this->load->view('MainBoard', true);
            } else {
                $this->load->view('MainBoard', true);
            }
        }
    }

    public function upload_post() {
        //$target_dir = "/opt/lampp/htdocs/jocomunicoapp/img/";
        $target_dir = "img/";
        $target_file = basename($_FILES['file']['name']);
        var_dump($_FILES);
        //MODIF: poner tamaño a 100 kb y tamaño 150
        if ($_FILES['file']['size'] > 1000) {
            $this->Img_Resize($_FILES['file']['tmp_name'], $target_dir, $target_file);
        } else {
            move_uploaded_file($_FILES['file']['tmp_name'], $target_dir . $target_file);
        }
    }

    function Img_Resize($src_path, $target_dir, $dst_path) {

        $x = getimagesize($src_path);
        
        $width = $x['0'];
        $height = $x['1'];

        $rs_width = $width / 2; //resize to half of the original width.
        $rs_height = $height / 2; //resize to half of the original height.

        switch ($x['mime']) {
            case "image/gif":
                $img = imagecreatefromgif($src_path);
                break;
            case "image/jpeg":
                $img = imagecreatefromjpeg($src_path);
                break;
            case "image/png":
                $img = imagecreatefrompng($src_path);
                break;
        }

        $img_base = imagecreatetruecolor($rs_width, $rs_height);
        
        switch ($x['mime']) {
            case "image/png":
                echo "si";
                // integer representation of the color black (rgb: 0,0,0)
                $background = imagecolorallocate($img_base, 0, 0, 0);
                // removing the black from the placeholder
                imagecolortransparent($img_base, $background);

                // turning off alpha blending (to ensure alpha channel information 
                // is preserved, rather than removed (blending with the rest of the 
                // image in the form of black))
                imagealphablending($img_base, false);

                // turning on alpha channel information saving (to ensure the full range 
                // of transparency is preserved)
                imagesavealpha($img_base, true);

                break;
            case "image/gif":
                // integer representation of the color black (rgb: 0,0,0)
                $background = imagecolorallocate($img_base, 0, 0, 0);
                // removing the black from the placeholder
                imagecolortransparent($img_base, $background);

                break;
        }
        imagecopyresampled($img_base, $img, 0, 0, 0, 0, $rs_width, $rs_height, $width, $height);

        switch ($x['mime']) {
            case "image/gif":
                imagegif($img_base, $target_dir.$dst_path);
                break;
            case "image/jpeg":
                imagejpeg($img_base, $target_dir.$dst_path);
                break;
            case "image/png":
                imagepng($img_base, $target_dir.$dst_path);
                break;
        }
        // If we have to resize the img again
        if (filesize($target_dir.$dst_path) > 50000) {
            // The new source img will be the last output img
            $newsrc_path = $target_dir.$dst_path;
            $newdst_path = "r".$dst_path;
            $this->Img_Resize($newsrc_path, $target_dir, $newdst_path);
            unlink($newsrc_path);
            rename($target_dir.$newdst_path, $newsrc_path);
        }
    }

}
