<?php
/**
 * Created by PhpStorm.
 * User: Marlon
 * Date: 2021/4/13
 * Time: 15:37
 */

namespace App\Task;

use App\Factory\DataokeFactory;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\ApplicationContext;

/**
 * @Crontab(name="Foo", rule="*\/5 * * * * *", callback="execute", memo="这是一个示例的定时任务")
 */
class FooTask
{
    /**
     * @Inject()
     * @var \Hyperf\Contract\StdoutLoggerInterface
     */
    private $logger;

    public function __construct(DataokeFactory $dataokeFactory)
    {
        $this->dataokeFactory = $dataokeFactory;
    }

    public function execute()
    {
        $this->logger->info(date('Y-m-d H:i:s', time()));
    }

    /**
     * @Crontab(rule="*\/5 * * * * *", memo="foo")
     */
    public function foo()
    {
        $container = ApplicationContext::getContainer();
        $redis = $container->get(\Hyperf\Redis\Redis::class);
        $min_id = $redis->get('min_id') ?? 1;
        $httpClient = $this->dataokeFactory->http();
        $response = $httpClient->get('http://v2.api.haodanku.com/itemlist/apikey/super/nav/3/cid/0/back/500/min_id/'.$min_id);
        $res = $response->getBody()->getContents();
        $res = json_decode($res, true);
        $redis->set('min_id',$res['min_id']);
        $client = ApplicationContext::getContainer()->get(MongoTask::class);
        if(count($res['data'])>0){
            foreach ($res['data'] as $val){
                $client->insert('hyperf.hdk', $val);
            }
        }else{
            echo "done";
        }

        echo $res['min_id'];
    }
}