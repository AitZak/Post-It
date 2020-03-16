<?php


namespace App\Api;


use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

class TumblrApi
{

    public function createData($content){
        $data = ['type'=> $content['type']];

        if($content['type'] == "txt"){
            $data['title'] = $content['title'];
            $data['body'] = $content['description'];
        }
        if($content['type'] == "photo"){
            $data['source'] = $content['file'];
        }
        if($content['type'] == "video"){
            $data['data'] = $content['title'];
        }
        return $data;

    }

//    public function post($data){
//       $stack = HandlerStack::create();
//        $middleware = new Oauth1([
//            'consumer_key' => 'sv0WYaafsfX3ZqUNoNzoV4kr9rcBpfZt3GOTXGkctdABqYqNmA',
//            'consumer_secret' => 'nQNKXWwz72hG3j5KCkt7fYApuvZjoJOr2zQlgyJ5WIeT3m8PVX',
//            'token' => 'PPWcCOUISQJHkYhdlvgpaYidIk7IBrRs4wuZvlkVzGEkrfF5OG',
//            'token_secret' => 'p709EckEIKYYk2mVVWp3iaAglVUSqAoutQHSVxTFmwx8Jn4YBB']);
//        $stack->push($middleware);
//
//        $client = new Client([
//            'base_uri' => 'https://api.tumblr.com/v2/',
//            'handler' => $stack,
//            'auth' => 'oauth',
//            'headers' => [ 'Content-Type' => 'application/json' ]
//        ]);
//
//
//        $res = $client->post('blog/androgynouskingtale/post',['body' => json_encode($data)]);
//        $end = json_decode($res->getBody(), true);
//        return $this->render('test.html.twig', [
//           'test' => $end,]);
//    }


//    public static function postText($data)
//    {
//
//        $data = ['type'     =>  "text",
//            'tags'     =>  "text,my first post",
//            'title'   =>  "First attempt",
//            'body' => "hat\'s all folks!"];
//
//        $stack = HandlerStack::create();
//
//        $middleware = new Oauth1([
//            'consumer_key' => 'sv0WYaafsfX3ZqUNoNzoV4kr9rcBpfZt3GOTXGkctdABqYqNmA',
//            'consumer_secret' => 'nQNKXWwz72hG3j5KCkt7fYApuvZjoJOr2zQlgyJ5WIeT3m8PVX',
//            'token' => 'PPWcCOUISQJHkYhdlvgpaYidIk7IBrRs4wuZvlkVzGEkrfF5OG',
//            'token_secret' => 'p709EckEIKYYk2mVVWp3iaAglVUSqAoutQHSVxTFmwx8Jn4YBB'
//        ]);
//        $stack->push($middleware);
//
//        $client = new Client([
//            'base_uri' => 'https://api.tumblr.com/v2/',
//            'handler' => $stack,
//            'auth' => 'oauth',
//            'headers' => [ 'Content-Type' => 'application/json' ]
//        ]);
//
//// Now you don't need to add the auth parameter
//        $res = $client->post('blog/androgynouskingtale/post',['body' => json_encode($data)]);
//        $end = json_decode($res->getBody(), true);
//        return $this->render('test.html.twig', [
//            'test' => $end,
//        ]);
//    }
}