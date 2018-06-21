<?php
/**
 * Created by PhpStorm.
 * User: Damon
 * Date: 2018/6/21
 * Time: 8:50
 */

namespace App\Models\Traits;


use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

trait LastActivedAtHelper
{
    //缓存相关
    protected $hash_prefix = 'bbs_last_actived_at_';
    protected $field_prefix = 'user_';

    public function recordLastActivedAt()
    {
        //获取今天的日期
        $date = Carbon::now()->toDateString();

        //Redis 哈希表的命名 ，如: bbs_last_actived_at_2018-06-21
        $hash = $this->hash_prefix . $date;

        //字段名称，如user_1
        $field = $this->field_prefix . $this->id;

        // 当前时间，如：2017-10-21 08:35:15
        $now = Carbon::now()->toDateTimeString();

        // 数据写入 Redis ，字段已存在会被更新
        Redis::hSet($hash, $field, $now);
    }

    public function syncUserActivedAt()
    {
        //获取昨天的日期
        $yesterday_date = Carbon::yesterday()->toDateString();

        //Redis 哈希表的命名 ，如: bbs_last_actived_at_2018-06-21
        $hash = $this->hash_prefix . $yesterday_date;

        // 从 Redis 中获取所有哈希表里的数据
        $dates = Redis::hGetAll($hash);

        //遍历，并同步进数据库
        foreach ($dates as $user_id => $actived_at) {
            //将user_1 => 1
            $user_id = str_replace($this->field_prefix,'',$user_id);
            //只有当用户存在时才更新到数据库中
            if($user = $this->find($user_id)) {
                $user->last_actived_at = $actived_at;
                $user->save();
            }
        }

        // 以数据库为中心的存储，既已同步，即可删除
        Redis::del($hash);
    }
}