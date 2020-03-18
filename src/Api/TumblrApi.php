<?php


namespace App\Api;


class TumblrApi
{
    public function createData($content){
        $data = ['type'=> $content['type']];

        if($content['type'] == "text"){
            $data['title'] = $content['title'];
            $data['body'] = $content['description'];
        }
        if($content['type'] == "photo"){
            $data['source'] =  "https://www.myprojectsdev.fr/uploads/files/".$content['file'];
        }
        if($content['type'] == "video"){
            $data['data'] = "https://www.myprojectsdev.fr/uploads/files/".$content['file'];
        }
        if($content['type'] == "link"){
            $data['title'] = $content['title'];
            $data['url'] = $content['descritpion'];
        }
        return $data;
    }
}