<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\Controller;
use App\Model\News;
use GTClient;
use GTNotification;
use GTPushMessage;
use GTPushRequest;
use Hyperf\DbConnection\Db;
use Hyperf\GoTask\MongoClient\MongoClient;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use App\Task\MongoTask;
use Hyperf\Utils\ApplicationContext;

/**
 * User: Marlon
 * Date: 2021/3/3
 * Time: 16:48
 * Class CaiController
 * @package App\Controller\Api
 * @AutoController()
 */
class CaiController extends Controller
{
    public function index(RequestInterface $request, ResponseInterface $response)
    {
        $name = $this->request->input('name','marlon');
        return $response->raw('Hello '.$name);
//        return News::paginate(20);

    }

    /**
     * Note: 获取菜谱列表接口
     * User: Marlon
     * Date: 2021/3/3
     * Time: 20:51
     * @return array
     */
    public function get_list()
    {
        $classid = (int) $this->request->input('classid',6);
        $limit = (int) $this->request->input('limit',20);
        $data = Db::table('news')->where('classid',$classid)->paginate($limit);
        return $this->success($data);
    }

    /**
     * Note: 获取菜谱详情接口
     * User: Marlon
     * Date: 2021/3/3
     * Time: 20:50
     * @return array
     */
    public function get_content()
    {
        $id = (int) $this->request->input('id',6);
        $data =  Db::table('news_data')->where('id',$id)->get();
        return $this->success($data);
    }

    /**
     * Note: 菜谱搜索接口
     * User: Marlon
     * Date: 2021/3/3
     * Time: 21:28
     * @return array
     */
    public function search()
    {
        $keyword = $this->request->input('keyword');
        $data = Db::table('news')->where('smalltext', 'like', '%'.$keyword.'%')->paginate(20);
        return $this->success($data);
    }


    /**
     * Note: 获取食材分类
     * User: Marlon
     * Date: 2021/3/3
     * Time: 21:40
     * @return array
     */
    public function get_shicai_type()
    {
        $bclassid = (int) $this->request->input('bclassid',33);
        $data =  Db::table('enewsclass')->where('bclassid',$bclassid)->get();
        return $this->success($data);
    }
    /**
     * Note: 获取食材子分类
     * User: Marlon
     * Date: 2021/3/3
     * Time: 21:40
     * @return array
     */
    public function get_shicai()
    {
        $classid = (int) $this->request->input('classid',38);
        try {
            $data = Db::table('shicai')->where('classid',$classid)->get()->map(function ($value, $key) {
                $value->titlepic = 'http://cpapi.0512688.com'.$value->titlepic;
                return $value;
            });
        } catch (\Throwable $throwable) {
            var_dump(get_class($throwable), $throwable->getMessage());
        }
        return $this->success($data);;
    }

    /**
     * Note: 消息推送
     * User: Marlon
     * Date: 2021/3/5
     * Time: 15:08
     */
    public function push()
    {
        //创建API，APPID等配置参考 环境要求 进行获取
        $api = new GTClient("https://restapi.getui.com",env('APPKEY'), env('APPID'),env('MASTERSECRET'));
        //设置推送参数
        $push = new GTPushRequest();
        $push->setRequestId(123123123);
        $message = new GTPushMessage();
        $notify = new GTNotification();
        $notify->setTitle("无油烤鸡柳的家常做法");
        $notify->setBody("前天下午化了块鸡胸肉，准备晚上做芹菜鸡丁打卤面");
        //点击通知后续动作，目前支持以下后续动作:
        //1、intent：打开应用内特定页面url：打开网页地址。2、payload：自定义消息内容启动应用。3、payload_custom：自定义消息内容不启动应用。4、startapp：打开应用首页。5、none：纯通知，无后续动作
        $notify->setClickType("url");
        $notify->setUrl("https://cpu.baidu.com/wap/1022/275535691/i?from=detail&pu=1&promotion_media_channel=79805&chk=1");
        $message->setNotification($notify);
        $push->setPushMessage($message);
//        $push->setCid("CID");
        //处理返回结果
        $result = $api->pushApi()->pushAll($push);
        return $result;
    }
    public function queryPushResultByDate(){
        //创建API，APPID等配置参考 环境要求 进行获取
        $api = new GTClient("https://restapi.getui.com",env('APPKEY'), env('APPID'),env('MASTERSECRET'));
        //处理返回结果
        $result = $api->statisticsApi()->queryPushResultByDate("2021-03-05");
        return $result;
    }

    /**
     * Note: mongo test
     * User: Marlon
     * Date: 2021/3/9
     * Time: 14:23
     * @param MongoClient $client
     * @return mixed
     */
    public function mongo(MongoClient $client)
    {
        $col = $client->my_database->my_col;
        $col->insertOne(['gender' => 'male', 'age' => 18]);
        $col->insertMany([['gender' => 'male', 'age' => 20], ['gender' => 'female', 'age' => 18]]);
        $col->countDocuments();
        $col->findOne(['gender' => 'male']);
        $col->find(['gender' => 'male'], ['skip' => 1, 'limit' => 1]);
        $col->updateOne(['gender' => 'male'], ['$inc' => ['age' => 1]]);
        $col->updateMany(['gender' => 'male'], ['$inc' => ['age' => 1]]);
        $col->replaceOne(['gender' => 'female'], ['gender' => 'female', 'age' => 15]);
        $col->aggregate([
            ['$match' => ['gender' => 'male']],
            ['$group' => ['_id' => '$gender', 'total' => ['$sum' => '$age']]],
        ]);
        $col->deleteOne(['gender' => 'male']);
        $col->deleteMany(['age' => 15]);
        $col->drop();
        // if there is a command not yet supported, use runCommand or runCommandCursor.
        $client->my_database->runCommand(['ping' => 1]);
        return $client->my_database->runCommandCursor(['listCollections' => 1]);
    }

    /**
     * Note: mongo task
     * User: Marlon
     * Date: 2021/3/9
     * Time: 14:45
     */
    public function mongo_test()
    {
        $client = ApplicationContext::getContainer()->get(MongoTask::class);
        $client->insert('hyperf.test', ['id' => rand(0, 99999999)]);
        $result = $client->query('hyperf.test', [], [
            'sort' => ['id' => -1],
            'limit' => 5,
        ]);
    }

    /**
     * Note: redis
     * User: Marlon
     * Date: 2021/3/9
     * Time: 21:31
     * @return array
     */
    public function redis()
    {
        $container = ApplicationContext::getContainer();

        $redis = $container->get(\Hyperf\Redis\Redis::class);
        $redis->set('marlon1','success1');
        $result = $redis->keys('*');
        return $this->success($result);
    }
}
