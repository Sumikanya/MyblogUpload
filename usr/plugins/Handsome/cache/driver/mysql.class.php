<?php
<<<<<<< HEAD
class HandsomeCache extends HandsomeCacheBase
{
    public function __construct($option)
    {
        parent::__construct($option);
=======
class MetingCache implements MetingCacheI
{
    private $db = null;
    public function __construct($option)
    {
        $this->db = Typecho_Db::get();
        $dbname = $this->db->getPrefix() . 'metingcache';
        $sql = "SHOW TABLES LIKE '%" . $dbname . "%'";
        if (count($this->db->fetchAll($sql)) == 0) {
            $this->install();
        } else {
            $this->db->query($this->db->delete('table.metingcache')->where('time <= ?', time()));
        }
>>>>>>> 12177ad2b7c543da838558647f6b4e552c8998f9
    }
    public function install()
    {
        $sql = '
DROP TABLE IF EXISTS `%dbname%`;
CREATE TABLE `%dbname%` (
  `key` char(32) NOT NULL,
<<<<<<< HEAD
  `data` longtext,
  `time` bigint(20) DEFAULT NULL,
  `type` char(10),
  PRIMARY KEY (`key`)
)  DEFAULT CHARSET=%charset%';
        $dbname = $this->db->getPrefix().$this->cache_db_name;
=======
  `data` mediumtext,
  `time` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=%charset%';
        $dbname = $this->db->getPrefix() . 'metingcache';
>>>>>>> 12177ad2b7c543da838558647f6b4e552c8998f9
        $search = array('%dbname%', '%charset%');
        $replace = array($dbname, str_replace('UTF-8', 'utf8', Helper::options()->charset));

        $sql = str_replace($search, $replace, $sql);
        $sqls = explode(';', $sql);
        foreach ($sqls as $sql) {
            $this->db->query($sql);
        }
    }
<<<<<<< HEAD

    public function is_exist_table()
    {
        $this->db = Typecho_Db::get();
        $dbname = $this->db->getPrefix() . $this->cache_db_name;
        $sql = "SHOW TABLES LIKE '%" . $dbname . "%'";
        if (count($this->db->fetchAll($sql)) == 0) {
            return false;
        }else{
            return true;
=======
    public function set($key, $value, $expire = 86400)
    {
        $this->db->query($this->db->insert('table.metingcache')->rows(array(
            'key' => md5($key),
            'data' => $value,
            'time' => time() + $expire
        )));
    }
    public function get($key)
    {
        $rs = $this->db->fetchRow($this->db->select('data')->from('table.metingcache')->where('key = ?', md5($key)));
        if (count($rs) == 0) {
            return false;
        } else {
            return $rs['data'];
        }
    }
    public function flush()
    {
        return $this->db->query($this->db->delete('table.metingcache'));
    }
    public function check()
    {
        $number = uniqid();
        $this->set('check', $number, 60);
        $cache = $this->get('check');
        if ($number != $cache) {
            throw new Exception('Cache Test Fall!');
>>>>>>> 12177ad2b7c543da838558647f6b4e552c8998f9
        }
    }
}
