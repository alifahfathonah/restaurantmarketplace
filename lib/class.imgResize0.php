<?php
function Image_resize($userfile_name, $userfile_tmp, $userfile_size, $userfile_type,$i,$postid)
{
     
	 $size = 110; // the thumbnail width
     $filedir = 'propertyimages/'; // the directory for the original image
	 $thumbdir = 'propertyimages/'; // the directory for the thumbnail image
     $prefix = 'small_'.$postid.'_'; // the prefix to be added to the original name
     $maxfile = '10000000';
     $mode = '0666';
	 
	 $pos = strrpos($userfile_name, ".");
	 //echo $pos;
	 
	 $image_type=strtolower(substr($userfile_name,$pos + 1));  
     if (isset($userfile_name))
     {
         $prod_img = strtolower($filedir.$postid.'_'.$userfile_name);

         $prod_img_thumb = strtolower($thumbdir.$prefix.$userfile_name);
         move_uploaded_file($userfile_tmp, $prod_img);
         chmod ($prod_img, octdec($mode));
         
         $sizes = getimagesize($prod_img);

         $aspect_ratio = $sizes[1]/$sizes[0];

         if ($sizes[0] <= $size)
         {
             $new_width = $sizes[0];
             $new_height = $sizes[1];
         }
		 else
		 {
             $new_width = $size;
			 $new_height = abs($new_width*$aspect_ratio);
         }

         $destimg=ImageCreateTrueColor($new_width,$new_height) or die('Problem In Creating image');
		 if($image_type=="jpg" || $image_type=="jpeg"){
		   	$srcimg=ImageCreateFromJPEG($prod_img) or die('Problem In opening Source Image');
		 }elseif($image_type=="gif"){
		 	$srcimg=ImageCreateFromGIF($prod_img) or die('Problem In opening Source Image');
		 }elseif($image_type=="png"){
		 	$srcimg=ImageCreateFromPNG($prod_img) or die('Problem In opening Source Image');
		 }else{
		 	$srcimg="";
		 }
		 //echo $srcimg;
		 if(!$srcimg == ""){
         	ImageCopyResized($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg)) or die('Problem In resizing');
		 	if($image_type=="jpg" || $image_type=="jpeg"){
	        	 ImageJPEG($destimg,$prod_img_thumb,90) or die('Problem In saving');
		 	}elseif($image_type=="gif"){
			 	ImageGIF($destimg,$prod_img_thumb,90) or die('Problem In saving');
		 	}elseif($image_type=="png"){
				 ImagePNG($destimg,$prod_img_thumb,90) or die('Problem In saving');
		 	}
    	
	     }
		 
		 
		 //list($width, $height, $type, $attr) = getimagesize($prod_img);
		if ($sizes[0]>520 || $sizes[1]>300){
		  
			  if ($sizes[1]>300 && $sizes[1]>$sizes[0]){
				 $aspect_ratio = $sizes[0]/$sizes[1];
				 $new_height = 300;
				 $new_width = abs($new_height*$aspect_ratio);
			  }else{
			       if ($sizes[0]>520){
					 $new_width = 520;
					 $new_height = abs($new_width*$aspect_ratio);
				  }
			  }
		  
			$prefix = 'large_'.$postid.'_';
			$prod_img_large=strtolower($thumbdir.$prefix.$userfile_name);
			 
			  $destimg=ImageCreateTrueColor($new_width,$new_height) or die('Problem In Creating image');
			 if($image_type=="jpg" || $image_type=="jpeg"){
				$srcimg=ImageCreateFromJPEG($prod_img) or die('Problem In opening Source Image');
			 }elseif($image_type=="gif"){
				$srcimg=ImageCreateFromGIF($prod_img) or die('Problem In opening Source Image');
			 }elseif($image_type=="png"){
				$srcimg=ImageCreateFromPNG($prod_img) or die('Problem In opening Source Image');
			 
			 }else{
				$srcimg="";
			 }
			 
			 if(!$srcimg == ""){
				ImageCopyResized($destimg,$srcimg,0,0,0,0,$new_width,$new_height,ImageSX($srcimg),ImageSY($srcimg)) or die('Problem In resizing');
				if($image_type=="jpg" || $image_type=="jpeg"){
					 ImageJPEG($destimg,$prod_img_large,90) or die('Problem In saving');
				}elseif($image_type=="gif"){
					ImageGIF($destimg,$prod_img_large,90) or die('Problem In saving');
				}elseif($image_type=="png"){
					 ImagePNG($destimg,$prod_img_large,90) or die('Problem In saving');
				}
			
			 }
		}
         unlink($prod_img);
		 imagedestroy($destimg);
     }
	
}

?>
