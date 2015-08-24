<?php namespace Ice;

abstract class Resource
{
    protected static    $Izberg = null;
    protected           $_name;

    public function __toString()
    {
        return json_encode($this);
    }

    private function setName($name = null)
    {
        /*
        ** If name isn't specified we convert the class name into ressource name.
        */
        if ($name === null)
        {
            $name = substr(str_replace("\\", "", get_class($this)), 3);
            $pieces = preg_split('/(?=[A-Z])/',$name);
            $final_str = "";
            foreach ($pieces as $piece)
            {
                if (strlen($final_str) > 0)
                    $final_str .= "_";
                $final_str .= strtolower($piece);
            }
        }
        else
            $final_str = $name;
        $this->_name = $final_str;
    }

    public function parseUri($uri)
    {
        if (strncmp("http", $uri, 4) == 0)
            $uri = substr($uri, strlen(self::$Izberg->getApiUrl()));
        $uri = explode('/', $uri);
        $uri = $uri[0];
        $tabname = explode('_', $uri);
        foreach ($tabname as &$value)
            $value = ucfirst($value);
        $uri = implode('', $tabname);
        return "Ice\\".$uri;
    }

    public function getName()
    {
        return ($this->_name);
    }

    public function __construct()
    {
        if (self::$Izberg === null)
            throw new Exception("Can't create instance of ".get_class().", no valid Izberg singleton");
        if (!$this->getName())
            $this->setName();
    }

    /**
    * Every Resource may have the current Izberg object as singleton
    * In order to contact the API
    *
    * @param Izberg Object
    *
    **/
    public static function setIzberg($instance)
    {
        self::$Izberg = $instance;
    }

    /**
    * The Log Function
    *
    * @param string $Message     Your log message
    * @param string [optional]   Log type (default is "ERROR")
    * @param string [optional]   Directory path for logs, CWD by default
    **/
    public function log($message, $level="error", $path = null)
    {
        date_default_timezone_set("Europe/berlin");
        if (false === Izberg::LOGS)
            return ;
        if (false === is_dir($path))
            $path = null;
        else if ($path && substr($path, -1) != '/')
            $path .= '/';
        file_put_contents($path."log-".$level."-".date("m-d").".txt", date("H:i:s | ")." : ".$message."\n", FILE_APPEND);
    }

    /**
    * Hydrate function
    *
    * @return void
    *
    **/
    public function hydrate($obj)
    {
        if (!$obj)
            return ;
        if (isset($obj->objects))
            $this->hydrate($obj->objects[0]);
        else
            foreach ($obj as $key=>$value)
            { 
                if (is_object($value))
                {
                    $classname = $this->parseUri($key);
                    if (!class_exists($classname) && isset($value->resource_uri))
                        $classname = $this->parseUri($value->resource_uri);

                    if (!class_exists($classname))
                        continue ;                    
                    else
                    {
                        $new_obj = new $classname();
                        $new_obj->hydrate($value);
                        $this->$key = $new_obj;
                    }
                }
                else if (is_array($value) && isset($value[0]->resource_uri))
                {
                    $classname = $this->parseUri($value[0]->resource_uri);
                    if (!class_exists($classname))
                        continue ;
                    $list = array();
                    foreach ($value as $val) {
                        $new_obj = new $classname();
                        $new_obj->hydrate($val);
                        $list[] = $new_obj;
                    }
                    $this->$key = $list;
                }
                else
                    $this->$key = $value;
            }
    }

    /**
    * Deletes Object
    *
    * @return Object
    *
    **/
    public function delete($params = null)
    { if (!$this->id)
            throw new Exception(__METHOD__." needs a valid ID");
        $name = $this->getName();
        return self::$Izberg->Call( $name . "/" . $this->id . "/", 'DELETE', $params, 'Content-Type: application/json');
    }

    /**
    * Updates Object
    *
    * @return Object
    *
    **/
    public function save()
    {
        if (!$this->resource_uri)
            return ;
        $data = $this;
        $data = json_encode($data);
        $data = (array)json_decode($data, true);
        if (!$this->id)
        {
            $response = self::$Izberg->Call($this->getName()."/", 'POST', $data);
            $this->hydrate($response);
            return ;
        }
        $url_params = parse_url($data["resource_uri"]);
        $addr = str_replace("/v1", "",$url_params["path"]);
        foreach ($data as $key=>$value)
            if (!$value || is_array($value))
                unset($data[$key]);

        return self::$Izberg->Call($addr, 'PUT', $data, "Content-Type: application/json");
    }
}
