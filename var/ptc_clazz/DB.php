<?php

class DB
{
    /**
     * @var mysqli|null
     */
    private static $db_conn = null;

    public function __construct(array $database)
    {
        if (!DB::$db_conn) {
            DB::$db_conn = new mysqli($database['host'], $database['uname'], $database['passwd'], $database['database'], $database['port']);

        }
    }

    /**
     * @param string $table
     * @param string|null $addition
     * @return bool|mysqli_result|void
     */
    public function select_all_from_table(string $table, string $addition = null)
    {
        if (is_null(self::$db_conn))
            return;
        $sql = "select * from $table $addition";
        return self::$db_conn->query($sql);
    }

    /**
     * @param array $data
     * @param string $table
     * @return bool|mysqli_result
     */
    public function insert_data2table(array $data, string $table)
    {
        $key = "";
        $value = "";
        foreach ($data as $k => $v) {
            $key .= $k . ',';
            $value .= $v . ',';
        }
        $key = substr($key, 0, strlen($key) - 1);
        $value = substr($value, 0, strlen($value) - 1);
        $sql = "insert into $table ($key) values ($value)";
        echo "$sql";
        return self::$db_conn->query($sql);

    }
    public function select(array $data,string $table,string $addition){
        $str='';
        foreach ($data as $v){
            $str.=$v.',';
        }
        $str=substr($str,0,strlen($str)-1);
        $sql="select $str from $table $addition";
        return self::$db_conn->query($sql);
    }
}