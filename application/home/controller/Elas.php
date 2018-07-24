<?php
/**
 * Created by PhpStorm.
 * User: lanco
 * Date: 2018/4/20
 * Time: 20:59
 */

namespace app\admin\controller;
use Elasticsearch\ClientBuilder;

class Elas
{
    private $api;
    private $config = [
        'hosts' => ['http://127.0.0.1:8000']
    ];
    public function _initialize()
    {
        $this->api = ClientBuilder::create()->setHosts($this->config['hosts'])->build();
    }
    function create_index(){
        //Elastic search php client
        $client = new Elasticsearch\Client();
        $goods = db('goods');

       $data = $goods->select();

        //delete index which already created
        $params = array();
        $params['index'] = 'lanco';
        $client->indices()->delete($params);

        //create index on log_date,src_ip,dest_ip
        $rtnCount = count($data);
        for($i=0;$i<$rtnCount;$i++){
            $params = array();
            $params['body'] = array(
                'log_date' => $data[$i]['log_date'],
                'src_ip' => $data[$i]['src_ip'],
                'dest_ip' => $data[$i]['dest_ip']
            );
            $params['index'] = 'log_index';
            $params['type'] = 'log_type';

            //Document will be indexed to log_index/log_type/autogenerate_id
            $client->index($params);
        }
        echo 'create index done!';
    }
    public function add_document()
    {
        $params = array();
        $params['body'] = array(
            'testField' => 'dfdsfdsf'
        );
        $params['index'] = 'my_index';
        $params['type'] = 'my_index';
        $params['id'] = 'w1231313';
        $ret = $this->client->index($params);
    }
    public function delete_index()
    {
        $deleteParams['index'] = 'my_index';
        $this->client->indices()->delete($deleteParams);
    }
    public function delete_document()
    {
        $deleteParams = array();
        $deleteParams['index'] = 'my_index';
        $deleteParams['type'] = 'my_index';
        $deleteParams['id'] = 'AU4Kmmj-WOmOrmyOj2qf';
        $retDelete = $this->client->delete($deleteParams);
    }
    public function update_document()
    {
        $updateParams = array();
        $updateParams['index'] = 'my_index';
        $updateParams['type'] = 'my_index';
        $updateParams['id'] = 'my_id';
        $updateParams['body']['doc']['asas']  = '111111';
        $response = $this->client->update($updateParams);

    }
    public function search()
    {
        $searchParams['index'] = 'my_index';
        $searchParams['type'] = 'my_index';
        $searchParams['from'] = 0;
        $searchParams['size'] = 100;
        $searchParams['sort'] = array(
            '_score' => array(
                'order' => 'desc'
            )
        );
        // $searchParams['body']['query']['match']['testField'] = 'abc';
        $retDoc = $this->client->search($searchParams);
        print_r($retDoc);
    }
    public function get_document()
    {
        $getParams = array();
        $getParams['index'] = 'my_index';
        $getParams['type'] = 'my_index';
        $getParams['id'] = 'AU4Kn-knWOmOrmyOj2qg';
        $retDoc = $this->client->get($getParams);
        print_r($retDoc);
    }

}