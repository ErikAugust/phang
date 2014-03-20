<?php
namespace models;
 
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;
 
class AppController {
    public function testAction() {
        return new Response("Hello world!");
    }
}
