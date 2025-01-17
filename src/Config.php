<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/28
 * Time: 下午5:46
 */

namespace EasySwoole\EasySwoole;


use EasySwoole\Component\Singleton;
use EasySwoole\Config\AbstractConfig;
use EasySwoole\Config\TableConfig;
use EasySwoole\Spl\SplArray;
use Swoole\Table;

class Config
{
    private $conf;

    use Singleton;

    public function __construct($isDev = true,?AbstractConfig $config = null)
    {
        if($config == null){
            $config = new TableConfig($isDev);
        }
        $this->conf = $config;
    }

    function storageHandler(AbstractConfig $config):Config
    {
        $this->conf = $config;
        return $this;
    }

    /**
     * 获取配置项
     * @param string $keyPath 配置项名称 支持点语法
     * @return array|mixed|null
     */
    public function getConf($keyPath = '')
    {
        if ($keyPath == '') {
            return $this->toArray();
        }
        return $this->conf->getConf($keyPath);
    }


    public function setConf($keyPath, $data): bool
    {
        return $this->conf->setConf($keyPath, $data);
    }


    public function toArray(): array
    {
        return $this->conf->getConf();
    }


    public function load(array $conf): bool
    {
        return $this->conf->load($conf);
    }

    public function merge(array $conf):bool
    {
        return $this->conf->merge($conf);
    }

    /**
     * 载入一个文件的配置项
     * @param string $filePath 配置文件路径
     * @param bool   $merge    是否将内容合并入主配置
     * @author : evalor <master@evalor.cn>
     */
    public function loadFile($filePath, $merge = false)
    {
        if (is_file($filePath)) {
            $confData = require_once $filePath;
            if (is_array($confData) && !empty($confData)) {
                $basename = strtolower(basename($filePath, '.php'));
                if (!$merge) {
                    $this->conf->setConf($basename,$confData);
                } else {
                    $this->conf->merge($confData);
                }
            }
        }
    }

    public function loadEnv(string $file)
    {
        if(file_exists($file)){
            $data = require $file;
            if(is_array($data)){
                $this->load($data);
            }
        }else{
            throw new \Exception("config file : {$file} is miss");
        }
    }

    public function clear():bool
    {
        return $this->conf->clear();
    }
}
