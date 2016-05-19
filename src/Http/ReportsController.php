<?php

namespace CityNexus\CityNexus\Http;

use App\Http\Controllers\Controller;
use CityNexus\CityNexus\Property;
use CityNexus\CityNexus\GenerateScore;
use CityNexus\CityNexus\Report;
use CityNexus\CityNexus\Score;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use CityNexus\CityNexus\Geocode;
use CityNexus\CityNexus\Table;


class ReportsController extends Controller
{

    public function getIndex()
    {
        $this->authorize('citynexus', ['reports', 'view']);

        $reports = Report::orderBy('name')->get();

        return view('citynexus::reports.index', compact('reports'));
    }

    public function getShow( $id )
    {
        $report = Report::find( $id );
        $settings = \GuzzleHttp\json_decode($report->settings);

        if($settings->type == 'Heat Map') {
            $table = Table::find($settings->table_id);
            return redirect(action('\CityNexus\CityNexus\Http\ReportsController@getHeatMap') . "?table=" . $settings->table_name . "&key=" . $settings->key . '&report_id=' . $id);
                }
    }

    public function getScatterChart()
    {
        $this->authorize('citynexus', ['reports', 'create']);
        $datasets = Table::where('table_title', "!=", 'null')->orderBy('table_name')->get(['table_name', 'table_title', 'id']);
        return view('citynexus::reports.charts.scatter_chart', compact('datasets'));

    }

    public function getDistributionCurve($table = null, $key = null, Request $request = null)
    {
        $this->authorize('citynexus', ['reports', 'create']);

        $datasets = Table::where('table_title', "!=", 'null')->orderBy('table_name')->get(['table_name', 'table_title', 'id']);

        if ($table != null && $key != null)
        {

            $max = DB::table($table)->max($key);

        if ($request->get('with_zeros')) {
            $data = DB::table($table)->orderBy($key)->lists($key);
            $min = DB::table($table)->min($key);
        } else {
            $data = DB::table($table)->where($key, '>', 0)->orderBy($key)->lists($key);
            $min = DB::table($table)->where($key, '>', 0)->min($key);
        }
        // Bern view
        $count = count($data);

        if ($request->get('feel') != null) {
            if ($request->get('feel') == 'bern') {
                $bern = $count - ($count / 100);
                $bern = intval($bern);
                $cutoff = $data[$bern];
            }

            if ($request->get('feel') == 'malthus') {
                $malthus = $count - ($count / 20);
                $malthus = intval($malthus);
                $cutoff = $data[$malthus];
            }

            if ($request->get('feel') == 'castro') {
                $castro = $count - ($count / 10);
                $castro = intval($castro);
                $cutoff = $data[$castro];
            }

            $data = DB::table($table)->where($key, '<', $cutoff)->where($key, '>', 0)->orderBy($key)->lists($key);
            $min = DB::table($table)->where($key, '<', $cutoff)->where($key, '>', 0)->min($key);
            $max = $cutoff;
            $count = count($data);
        }

        $zeros = DB::table($table)->where($key, '<=', '0')->count();
        $sum = DB::table($table)->sum($key);
        $middle = $count / 2;
        $firstQ = $count / 4;
        $thirdQ = $middle + $firstQ;
        $bTen = $count / 10;
        $tTen = $count - $bTen;

        $stats = [
            'max' => $max,
            'min' => $min,
            'count' => $count,
            'mean' => $sum / $count,
            'bTen' => $bTen,
            'firstQ' => $firstQ,
            'median' => $middle,
            'thirdQ' => $thirdQ,
            'tTen' => $tTen,
            'zeros' => $zeros,

        ];
            $table_ob = Table::where('table_name', $table)->first();
            $schema = \GuzzleHttp\json_decode($table_ob->scheme);
            $key_name = $schema->$key->name;
            $table_name = $table_ob->table_title;

            return view('citynexus::reports.charts.distribution_curve', compact('data', 'stats','table_name', 'key_name'));
        }

        else{
            $distribution = true;
            return view('citynexus::reports.charts.distribution_curve', compact('datasets', $distribution));

        }
    }

    public function getHeatMap(Request $request)
    {
        $this->authorize('citynexus', ['reports', 'view']);

        $datasets = Table::whereNotNull('table_title')->orderBy('table_title')->get();

        if($request->get('table') && $request->get('key'))
        {
            if(fnmatch('citynexus_scores_*', $request->get('table')))
            {
                $scores = Score::whereNotNull('name')->orderBy('name')->get(['id', 'name']);
                return view('citynexus::reports.maps.heatmap', compact('datasets', 'scores', 'report_id'))
                    ->with('table', $request->get('table'))
                    ->with('key', $request->get('key'));
            }
            $dataset = Table::where('table_name', $request->get('table'))->first();
            $scheme = \GuzzleHttp\json_decode($dataset->scheme);
            $report_id = null;
                if($request->get('report_id') != null) {
                    $report_id = $request->get('report_id');
                }
            return view('citynexus::reports.maps.heatmap', compact('datasets', 'dataset', 'scheme', 'report_id'))
                ->with('table', $request->get('table'))
                ->with('key', $request->get('key'));
        }
        else{
            return view('citynexus::reports.maps.heatmap', compact('datasets'));
        }
    }

    // Ajax Calls

    public function getDataFields($id, $axis = null, $type = null)
    {
        if($id == '_scores')
        {
            $scores = Score::orderBy('name')->get();

            if($type != null)
            {
                return view('citynexus::reports.includes.' .  $type . '._datafields', compact('scores', 'scheme'));
            }

            return view('citynexus::reports.includes.scatter._datafields', compact('scores', 'axis'));

        }
        $dataset = Table::find($id);

        $scheme = json_decode($dataset->scheme);

        if($type != null)
        {
            return view('citynexus::reports.includes.' . $type . '._datafields', compact('dataset', 'scheme'));

        }

        return view('citynexus::reports.includes.scatter._datafields', compact('dataset', 'scheme', 'axis'));
    }

    public function getHeatMapData($table, $key)
    {
        $raw_data = DB::table($table)
            ->where( $key, '>', '0')
            ->join('citynexus_properties', 'citynexus_properties.id', '=', 'property_id')
            ->join('citynexus_locations', 'citynexus_locations.id', '=', 'citynexus_properties.location_id')
            ->whereNotNull('citynexus_properties.location_id')
            ->select('citynexus_locations.lat', 'citynexus_locations.long', $table . '.' . $key)
            ->get();

        $max = DB::table($table)
            ->max($key);

        foreach($raw_data as $i)
        {
            $data[] =[$i->lat, $i->long, $i->$key/$max];
        }

        return $data;
    }

    public function getScatterDataSet($h_tablename, $h_key, $v_tablename, $v_key )
    {
        $return = null;

        // Build Horizontal Axis
        $horizontal = array_filter($this->getDataSet($h_tablename, $h_key));

        // Build Vertical Axis

        $vertical = array_filter($this->getDataSet($v_tablename, $v_key));


        // Build Combined Data

        $properties = Property::all()->lists('full_address', 'id');

        foreach($horizontal as $k => $i)
        {
            if(isset($vertical[$k])) {

                $return[] = [
                    'address' => $properties[$k],
                    'property_id' => $k,
                    'x' => $i,
                    'y' => $vertical[$k]];
            }
        }

        return $return;

    }

    private function getDataSet( $table_name, $key )
    {
        $return = null;

        $query_results = DB::table($table_name)->orderBy('created_at', 'desc')->lists($key, 'property_id');

        $return = $this->byPropertyId($query_results);

        return $return;
    }

    /**
     * @param $data array
     */
    private function byPropertyId($data)
    {
        $aliases = Property::whereNotNull('alias_of')->lists('id', 'alias_of');


        foreach($data as $k => $i)
        {
            if(isset($aliases->$k))
            {
                $return[$aliases->$k] = $i;
            }
            else
            {
                $return[$k] = $i;
            }
        }

        return $return;
    }

    public function postSaveReport(Request $request)
    {
        if($request->get('id') == null)
        {
            if(Report::where('name', $request->get('name'))->count() > 0)
            {
                $name = $request->get('name') . ' (' . Report::where('name', $request->get('name'))->count() . ")";
            }
            else{
                $name = $request->get('name');
            }
            $report = Report::create(['name' => $name, 'settings' => json_encode($request->get('settings'))]);
        }
        else
        {

            $report = Report::find($request->id);
            $report->settings = json_encode($request->get('settings'));
            $report->save();
        }

        return '<a onclick="updateReport(' . $report->id . ')" id="save-report" style="cursor: pointer"> Save Report Updates</a>';
    }
}