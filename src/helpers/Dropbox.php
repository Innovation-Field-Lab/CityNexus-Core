<?php


namespace CityNexus\CityNexus;

use jyggen\Curl;

class Dropbox
{
    public function getFileList($id)
    {
        $request = new Curl\Request('https://api.dropboxapi.com/2/files/list_folder');
        $request->headers("Authorization: Bearer ");
        $request->headers("Content-Type: application/json");
        $request->execute();

        dd($request);
    }
}