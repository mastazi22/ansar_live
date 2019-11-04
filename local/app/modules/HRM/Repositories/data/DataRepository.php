<?php
/**
 * Created by PhpStorm.
 * User: shuvo
 * Date: 4/4/2018
 * Time: 11:45 AM
 */

namespace App\modules\HRM\Repositories\data;


use App\modules\HRM\Models\AllEducationName;
use App\modules\HRM\Models\Blood;
use App\modules\HRM\Models\District;
use App\modules\HRM\Models\Division;
use App\modules\HRM\Models\OfferZone;
use App\modules\HRM\Models\Thana;
use App\modules\HRM\Models\Unions;
use Illuminate\Support\Facades\DB;

class DataRepository implements DataInterface
{
    public $division;
    public $unit;
    public $thana;
    public $union;
    public $bloodGroup;
    public $educationList;

    /**
     * DataRepository constructor.
     * @param Division $division
     * @param District $unit
     * @param Thana $thana
     * @param Unions $union
     * @param Blood $bloodGroup
     */
    public function __construct(Division $division,District $unit,Thana $thana,Unions $union,Blood $bloodGroup,AllEducationName $educationList)
    {
        $this->division = $division;
        $this->unit = $unit;
        $this->thana = $thana;
        $this->union = $union;
        $this->bloodGroup = $bloodGroup;
        $this->educationList = $educationList;
    }


    /**
     * @param string $id
     * @param string $offer_zone
     * @return mixed
     */
    public function getDivisions($id='',$offer_zone=false)
    {
        $division =  $this->division;
        if($id&&$id!='all'){
            $division = $division->whereEqualIn('id', $id);
        }
        if($offer_zone){
            $divisions = collect($division->where('id', '!=', 0)->orderBy('sort_by', 'asc')->get())->groupBy('id');
            $datas = [];
            foreach ($divisions as $k=>$div){
//                return gettype($div[0]);
                //$av = array_values($div);
                $of = OfferZone::where('range_id',$k)
                    ->select(DB::raw('GROUP_CONCAT(DISTINCT(offer_zone_range_id) SEPARATOR "-" ) as offer_zone_range'))
                    ->groupBy('range_id')->first();
                if($of){
                    $r = explode("-",$of->offer_zone_range);
                    $division_name_bng = $div[0]->division_name_bng;
                    $division_name_eng = $div[0]->division_name_eng;
                    $id = $div[0]->id;
                    $ids  = [$id];
                    foreach ($r as $rr){
                        $rv = $divisions[$rr];
                        $division_name_bng .= " + ".$rv[0]->division_name_bng;
                        $division_name_eng .= " + ".$rv[0]->division_name_eng;
                        array_push($ids,$rv[0]->id);
                    }
                    sort($ids);
                    $id = implode(",",$ids);
                    array_push($datas,compact('id','division_name_bng','division_name_eng'));
                }
                else{
                    array_push($datas,$div[0]);
                }
            }
            $divisions = [];
            $datas= collect($datas)->groupBy('id')->values()->toArray();
            foreach ($datas as $d){
                array_push($divisions,$d[0]);
            }
            return $divisions;
        }

        return $division->where('id', '!=', 0)->orderBy('sort_by', 'asc')->get();
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
            $units = $units->whereEqualIn('division_id',$range_id);
        }
        if($id&&$id!='all'){
            $units = $units->whereEqualIn('id', $id);
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
            $thanas = $thanas->whereEqualIn('division_id',$range_id);
        }
        if($unit_id&&$unit_id!='all'){
            $thanas = $thanas->whereEqualIn('unit_id',$unit_id);
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

    public function getBloodGroup(){
        return $this->bloodGroup->all();
    }
    public function getEducationList(){
        return $this->educationList->where('id','!=',0)->get();
    }
}