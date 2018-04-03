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
     * @return VDPAnsarInfo
     */
    public function create($input);

    /**
     * @param Request $input
     * @return mixed
     */
    public function update($input);

    /**
     * @param $id
     * @return VDPAnsarInfo
     */
    public function getInfo($id);

    /**
     * @param array $param
     * @param int $paginate
     * @return array VDPAnsarInfo
     */
    public function getInfos($param=[],$paginate=30);
}