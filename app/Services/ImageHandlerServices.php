<?php


namespace App\Services;

use File;

class ImageHandlerServices{


    public function saveOriginalImage($image){
        if($image == "") return;
        if(!File::isDirectory('/uploads')){
            File::makeDirectory('/uploads', 0777, true, true);
            $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
           // \Image::make($image)->save('./uploads/'.$name);
            \Image::make($image)->fit(200, 200)->save('uploads/200x200/'.$name)->destroy();
            \Image::make($image)->fit(50, 50)->save('uploads/50x50/'.$name)->destroy();
            return $name;
        }else{
            $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
           // \Image::make($image)->save('./uploads/'.$name);
            \Image::make($image)->fit(200, 200)->save('uploads/200x200/'.$name)->destroy();
            \Image::make($image)->fit(50, 50)->save('uploads/50x50/'.$name)->destroy();
            return $name;
        }
    }

}