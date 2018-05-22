<?php
/**
 * Created by PhpStorm.
 * User: shuvo
 * Date: 4/3/2018
 * Time: 1:22 PM
 */

namespace App\modules\AVURP\Repositories\VDPInfo;


use App\Http\Requests\Request;
use App\modules\AVURP\Models\VDPAnsarInfo;

interface VDPInfoInterface
{
    /**
     * @param Request $input
     * @param string $user_id
     * @return VDPAnsarInfo
     */
    public function create($input,$user_id='');

    /**
     * @param Request $input
     * @param $id
     * @param string $user_id
     * @return mixed
     */
    public function update($input,$id,$user_id='');

    /**
     * @param $id
     * @param string $user_id
     * @return VDPAnsarInfo
     */
    public function getInfo($id,$user_id='');

    /**
     * @param $id
     * @param string $user_id
     * @return VDPAnsarInfo
     */
    public function getInfoForEdit($id,$user_id='');

    /**
     * @param array $param
     * @param int $paginate
     * @param string $user_id
     * @param bool $is_api
     * @return array VDPAnsarInfo
     */
    public function getInfos($param=[],$paginate=30,$user_id='',$is_api=false);

    /**
     * @param id $
     * @return mixed
     */
    public function verifyVDP($id);
    public function approveVDP($id);
    public function verifyAndApproveVDP($id);
}