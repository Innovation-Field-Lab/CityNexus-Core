<?php


namespace CityNexus\CityNexus;

use CityNexus\CityNexus\Http\TablerController;
use Maatwebsite\Excel\Facades\Excel;

class Dropbox
{

    public function getFileList($settings)
    {
        $data =[
            'path' => $settings->path
        ];
        $post = json_encode($data);

        $url = 'https://api.dropboxapi.com/2/files/list_folder';
        $curl = curl_init($url); //initialise
        curl_setopt($curl,CURLOPT_HTTPHEADER,array('Authorization: Bearer ' . $settings->dropbox_token,'Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);

        $items = \GuzzleHttp\json_decode($response)->entries;

        $items = (object) $items;

        return $items;
    }


    public function processUpload($settings, $table_id, $path = null)
    {
        //Open Dropbox Connection
        $app = new DropboxApp($settings->dropbox_app, $settings->dropbox_secret, $settings->dropbox_token);

        //Copy dropbox file
        if($path == null)
        {
            $listFolderContents = \GrahamCampbell\Dropbox\Facades\Dropbox::listFolder($settings->path);
            $path = $listFolderContents->getLast()->display_path;
        }
        $file = \GrahamCampbell\Dropbox\Facades\Dropbox::download($path);
        $contents = $file->getContents();
        $filename = $file->getMetadata()->getName();
        file_put_contents(storage_path($filename), $contents);

        //process and delete temp file
        $data = Excel::load(storage_path($filename), function($reader){$reader->toArray();})->parsed;
        unlink(storage_path($filename));

        //Get Table
        $table = Table::find($table_id);

        $upload = Upload::create(['table_id' => $table_id, 'note' => 'Dropbox initial upload']);
        $tabler = new TablerController();
        $tabler->processUpload($table, $data, $upload->id);

        return view('citynexus::dataset.uploader.dropbox_success');

    }
}