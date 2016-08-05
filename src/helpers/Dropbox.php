<?php


namespace CityNexus\CityNexus;

use CityNexus\CityNexus\Http\TablerController;
use jyggen\Curl;
use Maatwebsite\Excel\Facades\Excel;

class Dropbox
{

    public function getFileList($settings)
    {
//        $app = ($settings->dropbox_app, $settings->dropbox_secret, $settings->dropbox_token);
        $listFolderContents = \GrahamCampbell\Dropbox\Facades\Dropbox::getAdapter();
        dd($listFolderContents);
        $items = $listFolderContents->getItems();
        $files = $items->all();

        return $files;
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