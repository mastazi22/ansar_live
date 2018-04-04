<?php
/**
 * Created by PhpStorm.
 * User: shuvo
 * Date: 4/4/2018
 * Time: 11:45 AM
 */

namespace App\modules\HRM\Repositories\data;


use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use App\modules\HRM\Models\Thana;
use App\modules\HRM\Models\Unions;

class DataRepository implements DataInterface
{
    public $division;
    public $unit;
    public $thana;
    public $union;
    /**
     * DataRepository constructor.
     * @param Division $division
     * @param District $unit
     * @param Thana $thana
     * @param Unions $union
     */
    public function __construct(Division $division,District $unit,Thana $thana,Unions $union)
    {
        $this->division = $division;
        $this->unit = $unit;
        $this->thana = $thana;
        $this->union = $union;
    }


    /**
     * @param string $id
     * @return mixed
     */
    public function getDivisions($id='')
    {
        if($id&&$id!='all'){
            $this->division->where('id', '=', $id);
        }
        return $this->division->where('id', '!=', 0)->orderBy('sort_by', 'asc')->get();
    }

    /**
     * @param string $range_id
     * @param string $id
     * @return mixed
     */
    public function getUnits($range_id = '',$id='')
    {
        $units = $this->unit;
        if($range_id&&$range_id!='all'){
            $units = $units->where('division_id',$range_id);
        }
        if($id&&$id!='all'){
            $units = $units->where('id', '=', $id);
        }
        return $units->where('id', '!=', 0)->get();
    }

    /**
     * @param string $range_id
     * @param string $unit_id
     * @param string $id
     * @return mixed
     */
    public function getThanas($range_id = '', $unit_id = '',$id='')
    {
        $thanas = $this->thana;
        if($range_id&&$range_id!='all'){
            $thanas = $thanas->where('division_id',$range_id);
        }
        if($unit_id&&$unit_id!='all'){
            $thanas = $thanas->where('unit_id',$unit_id);
        }
        if($id&&$id!='all'){
            $thanas = $thanas->where('id', '=', $id);
        }
        return $thanas->where('id', '!=', 0)->get();
    }

    /**
     * @param string $range_id
     * @param string $unit_id
     * @param string $thana_id
     * @param string $id
     * @return mixed
     */
    public function getUnions($range_id = '', $unit_id = '', $thana_id = '',$id='')
    {
        $unions = $this->union;
        if($range_id&&$range_id!='all'){
            $unions = $unions->where('division_id',$range_id);
        }
        if($unit_id&&$unit_id!='all'){
            $unions = $unions->where('unit_id',$unit_id);
        }
        if($thana_id&&$thana_id!='all'){
            $unions = $unions->where('thana_id',$thana_id);
        }
        if($id&&$id!='all'){
            $unions = $unions->where('id', '=', $id);
        }
        return $unions = $unions->where('id', '!=', 0)->get();
    }
}