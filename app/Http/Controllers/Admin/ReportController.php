<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    private $allowedReports = [
        'users'
    ];
    
    public function index(Request $request, $type)
    {
        if (!in_array($type, $this->allowedReports)) {
            throw new \Exception('Unsupported report type');
        }
        return $this->{$type . 'Report'}();
    }
    
    private function usersReport() {
        $data = [
            'dayly' => DB::table('users')
                ->select(
                    DB::raw("COUNT(`created_at`) AS `count`"), 
                    DB::raw("YEAR(`created_at`) AS `year`"),
                    DB::raw("MONTH(`created_at`) AS `month`"),
                    DB::raw("DAY(`created_at`) AS `day`")
                )
                ->groupBy('day')
                ->orderBy('created_at', 'asc')
                ->get()
                ->toArray()
            ,
            'weekly' => DB::table('users')
                ->select(
                    DB::raw("COUNT(`created_at`) AS `count`"), 
                    DB::raw("YEAR(`created_at`) AS `year`"),
                    DB::raw("WEEK(`created_at`) + 1 AS `week`")
                )
                ->groupBy('week')
                ->orderBy('created_at', 'asc')
                ->get()
                ->toArray()
            ,
            'monthly' => DB::table('users')
                ->select(
                    DB::raw("COUNT(`created_at`) AS `count`"), 
                    DB::raw("YEAR(`created_at`) AS `year`"),
                    DB::raw("MONTH(`created_at`) AS `month`")
                )
                ->groupBy('month')
                ->orderBy('created_at', 'asc')
                ->get()
                ->toArray()
            ,
        ];
        
        // Date range to build report
        $period = [
            // Y m d
            'from' => [2018, 1, 1],
            'to'   => [2018, date('n'), date('j')],
        ];
        
        $structurizedData =  
        $reportData = [
            'dayly' => [],
            'weekly' => [],
            'monthly' => [],
        ];
        
        // Reorganize dayly data to explode by year > month > day
        foreach ($data['dayly'] as $item) {
            if (empty($structurizedData['dayly'][$item->year])) {
                $structurizedData['dayly'][$item->year] = [];
            }
            if (empty($structurizedData['dayly'][$item->year][$item->month])) {
                $structurizedData['dayly'][$item->year][$item->month] = [];
            }
            if (empty($structurizedData['dayly'][$item->year][$item->month][$item->day])) {
                $structurizedData['dayly'][$item->year][$item->month][$item->day] = [];
            }
            $structurizedData['dayly'][$item->year][$item->month][$item->day] = $item->count;
        }
        
        // Merge report data with dayly report data
        for ($year = $period['from'][0]; $year <= $period['to'][0]; $year++)  {
            for ($month = $period['from'][1]; $month <= $period['to'][1]; $month++) {
                for ($day = $period['from'][2]; $day <= $period['to'][2]; $day++) {
                    $count = empty($structurizedData['dayly'][$year][$month][$day]) ? 0 : $structurizedData['dayly'][$year][$month][$day];
                    $reportData['dayly'][] = [
                        'count' => $count,
                        'label' => date('M', mktime(0, 0, 0, $month, 10)) . ' ' . $day . ', ' . $year
                    ];
                }
            }
        }
        
        // Reorganize weekly data to explode by year > week
        foreach ($data['weekly'] as $item) {
            if (empty($structurizedData['weekly'][$item->year])) {
                $structurizedData['weekly'][$item->year] = [];
            }
            if (empty($structurizedData['weekly'][$item->year][$item->week])) {
                $structurizedData['weekly'][$item->year][$item->week] = [];
            }
            $structurizedData['weekly'][$item->year][$item->week] = $item->count;
        }
        
        // Merge report data with weekly report data
        for ($year = $period['from'][0]; $year <= $period['to'][0]; $year++)  {
            for ($week =  (int)date('W', mktime(0, 0, 0, $period['from'][1], $period['from'][2], $period['from'][0])); 
                 $week <= (int)date('W', mktime(0, 0, 0, $period['to'][1], $period['to'][2], $period['to'][0])); 
                 $week++
            ) {
                $count = empty($structurizedData['weekly'][$year][$week]) ? 0 : $structurizedData['weekly'][$year][$week];
                
                // Get week begin timestamp
                $weekBeginTS = strtotime(sprintf('%dW%02d', $year, $week));
                
                // Get week end timestamp
                $weekEndTS = strtotime(sprintf('%dW%02d', $year, $week + 1)) - 1;
                
                // Get day start for the week
                $dayStart = date('j', $weekBeginTS);
                
                // Get day end for the week
                $dayEnd = date('j', $weekEndTS);
                
                // Get begin month for the week
                $monthStart = date('M', $weekBeginTS);
                
                // Get end month for the week
                $monthEnd   = date('M', $weekEndTS);
                
                $label = ($monthStart === $monthEnd) 
                    ? sprintf('%s %d-%d, %d', $monthStart, $dayStart, $dayEnd, $year) 
                    : sprintf('%s %d-%s %d, %d', $monthStart, $dayStart, $monthEnd, $dayEnd, $year);
                
                $reportData['weekly'][] = [
                    'count' => $count,
                    'label' => $label
                ];
            }
        }
        
        // Reorganize dayly data to explode by year > month > day
        foreach ($data['monthly'] as $item) {
            if (empty($structurizedData['monthly'][$item->year])) {
                $structurizedData['monthly'][$item->year] = [];
            }
            if (empty($structurizedData['monthly'][$item->year][$item->month])) {
                $structurizedData['monthly'][$item->year][$item->month] = [];
            }
            $structurizedData['monthly'][$item->year][$item->month] = $item->count;
        }
        
        // Merge report data with dayly report data
        for ($year = $period['from'][0]; $year <= $period['to'][0]; $year++)  {
            for ($month = $period['from'][1]; $month <= $period['to'][1]; $month++) {
                $count = empty($structurizedData['monthly'][$year][$month]) ? 0 : $structurizedData['monthly'][$year][$month];
                $reportData['monthly'][] = [
                    'count' => $count,
                    'label' => date('M', mktime(0, 0, 0, $month, 10)) . ' ' . $year
                ];
            }
        }
        
        /*
        dd($data);
        dd($structurizedData);
        dd($reportData);
        */
        
        return view('admin.reports.users', ['data' => $reportData]);
    }
}