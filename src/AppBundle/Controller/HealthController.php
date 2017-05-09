<?php

/**
 * Class HealthController
 *
 * This class controller provide /status/ url to check redis and mysql health
 *
 * PHP Version 7
 * @category Class
 * @author   Adegios <adegios@gmail.com>
 * @license  THE BEER-WARE LICENSE
 * @link
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Predis\Client as PredisClient;

class HealthController extends Controller
{
    /**
     * @Route("/status/", name="status")
     * @param Request $request the symfony request object
     * @return json
     */
    public function statusAction(Request $request)
    {
        //Redis
        try {
            $redisConf = $this->container->getParameter('redis');
            $redis = new PredisClient(
                [
                    'scheme' => $redisConf['scheme'],
                    'host' => $redisConf['host'],
                    'port' => $redisConf['port']
                ]
            );
            $redis->connect();
            $redisCheck = true;
        } catch (\Exception $e) {
            $redisCheck = false;
        }
        
        //Mysql
        try {
            $this->getDoctrine()->getConnection()->exec(
                'SELECT NOW() AS timenow FROM dual'
            );
            $mysqlCheck = true;
        } catch (\Exception $e) {
            $mysqlCheck = false;
        }

        return $this->json(
            [
                'APP' => $mysqlCheck && $redisCheck
                ,'MYSQL' => $mysqlCheck
                ,'REDIS' => $redisCheck
            ]
        );
    }
}