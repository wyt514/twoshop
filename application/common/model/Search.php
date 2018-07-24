<?php
/**
 * Created by PhpStorm.
 * User: lanco
 * Date: 2018/4/20
 * Time: 20:47
 */

namespace app\common\model;

use \Elasticsearch\ClientBuilder;
use think\Model;

class Search extends Model
{


    /**
     * Elasticsearch检索引擎模型
     */


    //配置
    private $config = [
        'hosts' => ['http://127.0.0.1:9200']
    ];
    private $api;
    public function __construct()
    {
        parent::__construct();
        //include(APP_PATH .'../vendor/autoload.php');
        //require_once EXTEND_PATH . 'org/elasticsearch/autoload.php';
        //import('org.elasticsearch.autoload', EXTEND_PATH);
        $this->api =ClientBuilder::create()->setHosts($this->config['hosts'])->build();
    }

    /*************************************************************
    /**
     * 索引一个文档
     * 说明：索引没有被创建时会自动创建索引
     */
    public function addOne()
    {
        $params = [

            'index' => 'lanco',
            'body' => [
                'settings' => [
                    'refresh_interval'=>'5s',
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0,
                    ],

                'mappings' => [
                    '_default_' => [
                        'properties' => [
                            '_all' =>[
                              'enabled'=>false
                            ]
                        ],
                        'strings' => [
                             'match_mapping_type' => 'string',
                             'mapping'=>[
                                 'type'=>'text',
                                 'analyzer'=>'ik_smart',
                                 'fields'=>[
                                     'keyword'=>[
                                         'type'=>'keyword'
                                     ]
                                 ]
                             ]
                        ]
                    ]

                ]
            ]
        ];

        $goods = db('goods');
        $data = $goods->where('issale=1')->select();
        $rtnCount = count($data);
        for($i=0;$i<$rtnCount;$i++){

            $params['body'] = [
                'goodsid' => $data[$i]['goodsid'],
                'catid' => $data[$i]['catid'],
                'goodsname' => $data[$i]['goodsname'],
                'marketprice' => $data[$i]['marketprice'],
                'shopprice' => $data[$i]['shopprice'],
                'laststock' => $data[$i]['laststock'],
                'goodstips' =>$data[$i]['goodstips'],
                'goodsdesc' =>$data[$i]['goodsdesc'],
                'salenum' =>$data[$i]['salenum'],
            ];

            $params['type'] = 'goods';

            //Document will be indexed to log_index/log_type/autogenerate_id
            $this->api->index($params);
        }
        return "create index done;";
       /* $params['index'] = 'lanco';
        $params['type']  = 'goods';
        $params['id']  = '20180407001';  # 不指定就是es自动分配
        $params['body']  = array('name' => '小川编程');*/
        //return $this->api->index($params);
    }

    /**
     * 索引多个文档
     * 说明：索引没有被创建时会自动创建索引
     */
    public function addAll()
    {
        $params = [];
        for($i = 1; $i < 21; $i++) {
            $params['body'][] = [
                'index' => [
                    '_index' => 'test_index'.$i,
                    '_type'  => 'cat_test',
                    '_id'    => $i,
                ]
            ];
            $params['body'][] = [
                'name' => '小川编程'.$i,
                'content' => '内容'.$i
            ];
        }
        return $this->api->bulk($params);
    }

    /**
     * 获取一个文档
     */
    public function getOne()
    {
        $params = [];
        $params['index'] = 'xiaochuan';
        $params['type']  = 'cat';
        $params['id']    = '20180407001';
        return $this->api->get($params);
    }

    /**
     * 搜索文档
     */
    public function search()
    {
        $params = [];
        $params['index'] = 'xiaochuan';
        $params['type']  = 'cat';
        $params['body']['query']['match']['name'] = '小川编程';
        return $this->api->search($params);
    }

    /**
     * 删除文档
     * 说明：文档删除后，不会删除对应索引。
     */
    public function delete()
    {
        $params = [];
        $params['index'] = 'xiaochuan';
        $params['type'] = 'cat';
        $params['id'] = '20180407001';
        return $this->api->delete($params);
    }

    /*************************************************************
    /**
     * 创建索引
     */
    public function createIndex()
    {
        $params = [];
        $params['index']  = 'xiaochuan';
        return $this->api->indices()->create($params);
    }

    /**
     * 删除索引：匹配单个 | 匹配多个
     * 说明： 索引删除后，索引下的所有文档也会被删除
     */
    public function deleteIndex()
    {
        $params = [];
        $params['index'] = 'test_index';  # 删除test_index单个索引
        #$params['index'] = 'test_index*'; # 删除以test_index开始的所有索引
        return $this->api->indices()->delete($params);
    }

    /*************************************************************
    /**
     * 设置索引配置
     */
    public function setIndexConfig()
    {
        $params = [];
        $params['index'] = 'xiaochuan';
        $params['body']['index']['number_of_replicas'] = 0;
        $params['body']['index']['refresh_interval'] = -1;
        return $this->api->indices()->putSettings($params);
    }

    /**
     * 获取索引配置
     */
    public function getIndexConfig()
    {
        # 单个获取条件写法
        $params['index'] = 'xiaochuan';
        # 多个获取条件写法
        //$params['index'] = ['xiaochuan', 'test_index'];
        return $this->api->indices()->getSettings($params);
    }

    /**
     * 设置索引映射
     */
    public function setIndexMapping()
    {
        #  设置索引和类型
        $params['index'] = 'xiaochuan';
        $params['type']  = 'cat';

        #  向现有索引添加新类型
        $myTypeMapping = array(
            '_source' => array(
                'enabled' => true
            ),
            'properties' => array(
                'first_name' => array(
                    'type' => 'string',
                    'analyzer' => 'standard'
                ),
                'age' => array(
                    'type' => 'integer'
                )
            )
        );
        $params['body']['cat'] = $myTypeMapping;

        #  更新索引映射
        $this->api->indices()->putMapping($params);
    }

    /**
     * 获取索引映射
     */
    public function getIndexMapping()
    {
        #  获取所有索引和类型的映射
        $ret = $this->api->indices()->getMapping();

        /*
        #  获取索引为：xiaochuan的映射
        $params['index'] = 'xiaochuan';
        $ret = $this->api->indices()->getMapping($params);

        #  获取类型为：cat的映射
        $params['type'] = 'cat';
        $ret = $this->api->indices()->getMapping($params);

        #  获取（索引为：xiaochuan和 类型为：cat）的映射
        $params['index'] = 'xiaochuan';
        $params['type']  = 'cat'
        $ret = $this->api->indices()->getMapping($params);

        #  获取索引为：xiaochuan和test_index的映射
        $params['index'] = ['xiaochuan', 'test_index'];
        $ret = $this->api->indices()->getMapping($params);
        */

        return $ret;
    }



}