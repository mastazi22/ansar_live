<?php
/**
 * Created by PhpStorm.
 * User: shuvo
 * Date: 4/4/2018
 * Time: 11:40 AM
 */

namespace App\modules\HRM\Repositories\data;


interface DataInterface
{
    /**
     * @param string $id
     * @return mixed
     */
    public function getDivisions($id='');

    /**
     * @param string $range_id
     * @param string $id
     * @return mixed
     */
    public function getUnits($range_id='',$id='');

    /**
     * @param string $range_id
     * @param string $unit_id
     * @param string $id
     * @return mixed
     */
    public function getThanas($range_id='', $unit_id='',$id='');

    /**
     * @param string $range_id
     * @param string $unit_id
     * @param string $thana_id
     * @param string $id
     * @return mixed
     */
    public function getUnions($range_id='', $unit_id='', $thana_id='',$id='');

    /**
     * @return mixed
     */
    public function getBloodGroup();

    /**
     * @return mixed
     */
    public function getEducationList();

}