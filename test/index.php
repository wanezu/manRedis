<?php  
class ActiveDate  
{
    private $redisConf = array('host' => 'localhost', 'port' => 6379);  
    private $redis = null;  
    private $userPrefix = 'user_active_';  
    /** 
     * 设置用户某天登录过 
     */  
    public function setActiveDate($userId, $time = null)  
    {
        if (empty($time)) {
            $time = time();
        }
        $redis = $this->getRedis();
        $redis->setBit($this->userPrefix . $userId . '_' . date('Y-m', $time), intval(date('d', $time)) - 1, 1);
        return true;
    }
    /** 
     * 得到用户本月登录天数 
     * redis >= 2.6.0 才可以 
     */  
    public function getActiveDatesCount($userId, $time = null){  
        if (empty($time)) {  
            $time = time();  
        }
        $redis = $this->getRedis();
        return $redis->bitcount($this->userPrefix . $userId . '_' . date('Y-m', $time));
    }  
    /** 
     * 得到用户某月所有的登录过日期 
     */  
    public function getActiveDates($userId, $time = null)  
    {
		if (!function_exists('cal_days_in_month')) 
		{ 
			function cal_days_in_month($calendar, $month, $year) 
			{ 
				return date('t', mktime(0, 0, 0, $month, 1, $year)); 
			}
		}
		if (!defined('CAL_GREGORIAN')) 
			define('CAL_GREGORIAN', 1); 
        $result = array();
        if (empty($time)) {
            $time = time();
        }
        $redis = $this->getRedis();  
        $strData = $redis->get($this->userPrefix . $userId . '_' . date('Y-m', $time));
        if (empty($strData)) {  
            return $result;  
        }
        $monthFirstDay = mktime(0, 0, 0, date("m", $time), 1, date("Y", $time));  
        $maxDay = cal_days_in_month(CAL_GREGORIAN, date("m", $time), date("Y", $time));
        $charData = unpack("C*", $strData);  
        for ($index = 1; $index <= count($charData); $index++) {
            for ($bit = 0; $bit < 8; $bit++) {
                if ($charData[$index] & 1 << $bit) {  
                    //$intervalDay = ($index - 1) * 8 + 8-$bit;  
                    $intervalDay = $index  * 8 -$bit;  
                    //如果数据有大于当月最大天数的时候  
                    if ($intervalDay > $maxDay) {  
                        return $result;
                    }
                    $result [] = date('Y-m-d', $monthFirstDay + ($intervalDay-1) * 86400);
                }
            }
        }
        return $result;
    }
    /** 
     *  redis连接 
     */  
    private function getRedis()  
    {  
        if (empty($this->redis)) {  
            $redis = new Redis();
            if (!$redis->connect($this->redisConf['host'], $this->redisConf['port'])) {
                throw new Exception("Error Redis Connect", 100);  
            }
            $redis->select(3);
            $this->redis = $redis;
        }
        return $this->redis;
    }
	
	public function getUser($userId , $time){
		$redis = $this->getRedis();
		return $redis->get($this->userPrefix . $userId . '_' . date('Y-m', $time));
	}
}
  
  
$activeDate = new ActiveDate();

var_dump($activeDate->setActiveDate(51454077,1438358460));//  2014/8/1 0:3:20  
var_dump($activeDate->setActiveDate(51454077,1438444860));//  2014/8/5 16:0:0  
var_dump($activeDate->setActiveDate(51454077,1438531260));//  2014/8/31 16:0:0  

var_dump($activeDate->getActiveDates(51454077));  

var_dump($activeDate->getActiveDatesCount(51454077));
var_dump($activeDate->getUser(51454077,1438531260));