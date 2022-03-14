<?php

namespace Logan\Jiangmen;

use GuzzleHttp\Client as HttpClient;
use Logan\Jiangmen\exceptions\InitRuntimeException;

class Client
{
    /**
     * 平台域名
     *
     * @var string
     */
    protected $domain;

    /**
     * 请求接口使用的 key
     *
     * @var string
     */
    protected $key;

    /**
     * 请求接口使用的 token
     *
     * @var string
     */
    protected $token;

    /**
     * 当前请求接口的时间日期
     *
     * @var string Y-m-d H:i:s
     */
    protected $dateTime;

    /**
     * 当前请求接口的签名
     *
     * @var string
     */
    protected $sign;

    /**
     * 地区版
     * 默认 0->通用版, 1->河南版，8->黔南
     *
     * @var int
     */
    protected $serverType;

    /**
     * GuzzleHttp 实例
     *
     * @var GuzzleHttp\Client
     */
    protected $httpClient = null;

    public function __construct(string $domain, array $config, $serverType = 0)
    {
        $domain = rtrim($domain, '/');

        if (!array_key_exists('key', $config)) {
            throw new InitRuntimeException("config key not null", 0);
        }
        if (!array_key_exists('token', $config)) {
            throw new InitRuntimeException("config token not null", 0);
        }

        $this->domain     = $domain;
        $this->key        = $config['key'];
        $this->token      = $config['token'];
        $this->serverType = $serverType;
        $this->httpClient = new HttpClient();

        // 设置请求时间
        $this->setDateTime();
    }

    /**
     * 设置请求时间
     *
     * @param int $timestamp    时间戳,默认当前时间戳 +1 s
     * @return void
     * @author LONG <1121116451@qq.com>
     * @version version
     * @date 2022-01-26
     */
    public function setDateTime(int $timestamp = 0)
    {
        if ($timestamp === 0) {
            $timestamp = time();
        }
        $this->dateTime = date('Y-m-d H:i:s', $timestamp);
        $this->setSign();
        return $this;
    }

    /**
     * 获取请求时间
     *
     * @return void
     * @author LONG <1121116451@qq.com>
     * @version version
     * @date 2022-01-26
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * 生成签名
     *
     * @return void
     * @author LONG <1121116451@qq.com>
     * @version version
     * @date 2022-01-27
     */
    public function setSign()
    {
        $this->sign = md5($this->token . $this->key . $this->dateTime);
        return $this;
    }

    /**
     * 获取签名值
     *
     * @return void
     * @author LONG <1121116451@qq.com>
     * @version version
     * @date 2022-01-27
     */
    public function getSign()
    {
        return $this->sign;
    }


    /**
     * 查询所有/单个班组
     *
     * @param string $code  班组编号
     * @return void
     * @author LONG <1121116451@qq.com>
     * @version version
     * @date 2022-02-08
     */
    public function queryGroup(string $code)
    {
        $url = $this->domain . '/api/Team/Query';

        $req = [
            'token'       => $this->token,
            'date'        => $this->dateTime,
            'sign'        => $this->sign,
            'serverType'  => $this->serverType,
            'requestJson' => [
                'teamNo' => $code
            ],
        ];

        $response = $this->httpClient->request('POST', $url, [
            'json' => $req
        ])
            ->getBody()
            ->getContents();

        return json_decode($response, true);
    }

    /**
     * 新增班组
     *
     * @param array $groupInfo
     * @param array|null $headMan
     * @return void
     * @author LONG <1121116451@qq.com>
     * @version version
     * @date 2022-02-08
     */
    public function addGroup(array $groupInfo, ?array $headMan = [])
    {
        $url = $this->domain . '/api/Team/Add';

        $req = [
            'token'       => $this->token,
            'date'        => $this->dateTime,
            'sign'        => $this->sign,
            'serverType'  => $this->serverType,
            'requestJson' => [
                'teamInfo' => $groupInfo,
                'headMan'  => $headMan ? $headMan : NULL
            ],
        ];

        $response = $this->httpClient->request('POST', $url, [
            'json' => $req
        ])
            ->getBody()
            ->getContents();

        return json_decode($response, true);
    }

    /**
     * 修改班组信息
     *
     * @param string $code  班组编号
     * @param array $teamInfo   班组信息
     * @return void
     * @author LONG <1121116451@qq.com>
     * @version version
     * @date 2022-02-08
     */
    public function updateGroup(string $code, array $groupInfo)
    {
        $url = $this->domain . '/api/Team/Update';

        $req = [
            'token'       => $this->token,
            'date'        => $this->dateTime,
            'sign'        => $this->sign,
            'serverType'  => $this->serverType,
            'requestJson' => [
                'teamNo'   => $code,
                'teamInfo' => $groupInfo
            ],
        ];

        $response = $this->httpClient->request('POST', $url, [
            'json' => $req
        ])
            ->getBody()
            ->getContents();

        return json_decode($response, true);
    }

    /**
     * 更换班组的班组长
     * 2021-06-10 接口弃用
     * 可通过更新项目工人接口【/api/ProjectWorker/Update】的 isLeader 参数更新
     *
     * @param string $code  班组编号
     * @param string $idCode    工人身份编号 DES
     * @return void
     * @author LONG <1121116451@qq.com>
     * @version version
     * @date 2022-02-10
     */
    public function updateGroupHeadMan(string $code, string $idCode)
    {
        $url = $this->domain . '/api/Team/HeadManUpdate';

        $req = [
            'token'       => $this->token,
            'date'        => $this->dateTime,
            'sign'        => $this->sign,
            'serverType'  => $this->serverType,
            'requestJson' => [
                'teamNo'   => $code,
                'identityCode' => $idCode
            ],
        ];

        $response = $this->httpClient->request('POST', $url, [
            'json' => $req
        ])
            ->getBody()
            ->getContents();

        return json_decode($response, true);
    }

    /**
     * 班组退场
     *
     * @param string $code  班组编号
     * @param string|null $date  退场日期
     * @param array|null $attachments    退场图片,有退场日期时，此字段必填。不超过 5 张 800kb 的图片 base64 字符串集合
     * @return void
     * @author LONG <1121116451@qq.com>
     * @version version
     * @date 2022-02-08
     */
    public function exitGroup(string $code, ?string $date, ?array $attachments)
    {
        $url = $this->domain . '/api/Team/Exit';

        $req = [
            'token'       => $this->token,
            'date'        => $this->dateTime,
            'sign'        => $this->sign,
            'serverType'  => $this->serverType,
            'requestJson' => [
                'teamNo'          => $code,
                'exitTime'        => $date,
                'exitAttachments' => $attachments
            ],
        ];

        $response = $this->httpClient->request('POST', $url, [
            'json' => $req
        ])
            ->getBody()
            ->getContents();

        return json_decode($response, true);
    }

    /**
     * 查询工人
     *
     * @param string $idCode    工人身份证号码 DES
     * @param string|null $code  工人编号。身份证号和编号必填其一，同时填写则默认为身份证号码
     * @return void
     * @author LONG <1121116451@qq.com>
     * @version version
     * @date 2022-02-09
     */
    public function queryWorkerInfo(string $idCode, ?string $code)
    {
        $url = $this->domain . '/api/Worker/Query';

        $req = [
            'token'       => $this->token,
            'date'        => $this->dateTime,
            'sign'        => $this->sign,
            'serverType'  => $this->serverType,
            'requestJson' => [
                'identityCode' => $idCode,
                'workerNo'     => $code
            ],
        ];

        $response = $this->httpClient->request('POST', $url, [
            'json' => $req
        ])
            ->getBody()
            ->getContents();

        return json_decode($response, true);
    }

    /**
     * 添加工人信息
     *
     * @param array $requestJson
     * @return void
     * @author LONG <1121116451@qq.com>
     * @version version
     * @date 2022-01-27
     */
    public function addWorkerInfo(array $requestJson)
    {
        $url = $this->domain . '/api/Worker/Add';

        $req = [
            'token'       => $this->token,
            'date'        => $this->dateTime,
            'sign'        => $this->sign,
            'serverType'  => $this->serverType,
            'requestJson' => $requestJson,
        ];

        $response = $this->httpClient->request('POST', $url, [
            'json' => $req
        ])
            ->getBody()
            ->getContents();

        return json_decode($response, true);
    }

    /**
     * 编辑工人信息
     *
     * @param string $code  工人编号
     * @param array $requestJson    工人详细信息数组
     * @return void
     * @author LONG <1121116451@qq.com>
     * @version version
     * @date 2022-02-09
     */
    public function updateWorkerInfo(string $code, array $requestJson)
    {
        $url = $this->domain . '/api/Worker/Update';

        $requestJson = $requestJson + ['workerNo' => $code];
        $req = [
            'token'       => $this->token,
            'date'        => $this->dateTime,
            'sign'        => $this->sign,
            'serverType'  => $this->serverType,
            'requestJson' => $requestJson,
        ];

        $response = $this->httpClient->request('POST', $url, [
            'json' => $req
        ])
            ->getBody()
            ->getContents();

        return json_decode($response, true);
    }

    /**
     * 查询项目工人信息
     *
     * @param int $page 指定页号，以0为起始数字，表示第1页
     * @param int $pageSize 每页记录数，默认20。最多不能超过50
     * @param string $code|null  班组编号
     * @param string $idCode|null    工人身份证号
     * @return void
     * @author LONG <1121116451@qq.com>
     * @version version
     * @date 2022-02-08
     */
    public function queryProjectWorker(int $page = 0, int $pageSize = 20, ?string $code, ?string $idCode)
    {
        $url = $this->domain . '/api/ProjectWorker/Query';

        $req = [
            'token'       => $this->token,
            'date'        => $this->dateTime,
            'sign'        => $this->sign,
            'serverType'  => $this->serverType,
            'requestJson' => [
                'pageIndex'    => $page,
                'pageSize'     => $pageSize,
                'teamNo'       => $code,
                'identityCode' => $idCode
            ],
        ];

        $response = $this->httpClient->request('POST', $url, [
            'json' => $req
        ])
            ->getBody()
            ->getContents();

        return json_decode($response, true);
    }

    /**
     * 添加项目工人
     * 工人进场
     *
     * @param array $requestJson
     * @return void
     * @author LONG <1121116451@qq.com>
     * @version version
     * @date 2022-02-08
     */
    public function addProjectWorker(array $requestJson)
    {
        $url = $this->domain . '/api/ProjectWorker/Add';

        $req = [
            'token'       => $this->token,
            'date'        => $this->dateTime,
            'sign'        => $this->sign,
            'serverType'  => $this->serverType,
            'requestJson' => $requestJson,
        ];

        $response = $this->httpClient->request('POST', $url, [
            'json' => $req
        ])
            ->getBody()
            ->getContents();

        return json_decode($response, true);
    }

    /**
     * 修改项目工人
     *
     * @param array $workerInfo 项目工人基础信息
     * @param array $contractInfo   项目工人合同信息
     * @param array $bankCardInfo   项目工人银行卡信息
     * @return void
     * @author LONG <1121116451@qq.com>
     * @version version
     * @date 2022-02-08
     */
    public function updateProjectWorker(array $workerInfo, array $contractInfo = [], array $bankCardInfo = [])
    {
        $url = $this->domain . '/api/ProjectWorker/Update';

        $req = [
            'token'       => $this->token,
            'date'        => $this->dateTime,
            'sign'        => $this->sign,
            'serverType'  => $this->serverType,
            'requestJson' => $workerInfo + ['contractInfo' => $contractInfo ?: NULL] + ['bankCardInfo' => $bankCardInfo ?: NULL],
        ];

        $response = $this->httpClient->request('POST', $url, [
            'json' => $req
        ])
            ->getBody()
            ->getContents();

        return json_decode($response, true);
    }

    /**
     * 项目工人退场
     * 工人退场
     *
     * @param string $code  班组编号
     * @param string $idCode    工人身份证号
     * @param string $date  退场日期 格式yyyy-MM-dd
     * @param string|null $attachment    退场图片。 不超过 50kb 的图片 base64 字符串。
     * @return void
     * @author LONG <1121116451@qq.com>
     * @version version
     * @date 2022-02-08
     */
    public function exitProjectWorker(string $code, string $idCode, string $date, ?string $attachment)
    {
        $url = $this->domain . '/api/ProjectWorker/Exit';

        $req = [
            'token'       => $this->token,
            'date'        => $this->dateTime,
            'sign'        => $this->sign,
            'serverType'  => $this->serverType,
            'requestJson' => [
                'teamNo'          => $code,
                'identityCode'    => $idCode,
                'exitTime'        => $date,
                'exitAttachments' => $attachment,
            ],
        ];

        $response = $this->httpClient->request('POST', $url, [
            'json' => $req
        ])
            ->getBody()
            ->getContents();

        return json_decode($response, true);
    }


    /**
     * 查询工人考勤
     *
     * @param string $date  查询日期。 格式yyyy-MM-dd
     * @param int $page 指定页号，以0为起始数字，表示第1页
     * @param int $pageSize 每页记录数，最多不能超过50
     * @param string $code  班组编号
     * @param string $idCode    工人身份证号 DES
     * @return void
     * @author LONG <1121116451@qq.com>
     * @version version
     * @date 2022-02-08
     */
    public function queryAttendance(string $date, int $page = 0, int $pageSize = 20, string $code = '', string $idCode = '')
    {
        $url = $this->domain . '/api/WorkerAttendance/Query';

        $req = [
            'token'       => $this->token,
            'date'        => $this->dateTime,
            'sign'        => $this->sign,
            'serverType'  => $this->serverType,
            'requestJson' => [
                'pageIndex'    => $page,
                'pageSize'     => $pageSize,
                'date'         => $date,
                'teamNo'       => $code,
                'identityCode' => $idCode
            ],
        ];

        $response = $this->httpClient->request('POST', $url, [
            'json' => $req
        ])
            ->getBody()
            ->getContents();

        return json_decode($response, true);
    }

    /**
     * 工人考勤
     *
     * @param string $idCode    工人身份证号 DES
     * @param bool $isIn    是否为进场
     * @param string $dateTime  工人打卡时间 格式yyyy-MM-dd HH:mm:ss , 不得大于接口请求时间
     * @param string $imageBase64   刷卡近照。不超过50kb的图片base64字符串
     * @param string $attendType    通行方式
     * @param string $channel   通道的名称
     * @param float $longitude  WGS84经度
     * @param float $latitude   WGS84纬度
     * @param string $remark    备注
     * @return void
     * @author LONG <1121116451@qq.com>
     * @version version
     * @date 2022-02-08
     */
    public function addAttendance(
        string $idCode,
        bool $isIn,
        string $dateTime,
        string $imageBase64 = '',
        string $attendType = '',
        string $channel = '',
        float $longitude = NULL,
        float $latitude = NULL,
        string $remark = ''
    ) {
        $url = $this->domain . '/api/WorkerAttendance/Add';

        $req = [
            'token'       => $this->token,
            'date'        => $this->dateTime,
            'sign'        => $this->sign,
            'serverType'  => $this->serverType,
            'requestJson' => [
                'identityCode' => $idCode,
                'type'         => $isIn ? 1 : 0,
                'checkDate'    => $dateTime,
                'image'        => $imageBase64 ?: NULL,
                'attendType'   => $attendType ?: '011', // 011->其他方式
                'channel'      => $channel ?: NULL,
                'lng'          => $longitude,
                'lat'          => $latitude,
                'other'        => $remark ?: NULL,
            ],
        ];
        if ($longitude === NULL) unset($req['requestJson']['lng']);
        if ($latitude  === NULL) unset($req['requestJson']['lat']);

        $response = $this->httpClient->request('POST', $url, [
            'json' => $req
        ])
            ->getBody()
            ->getContents();

        return json_decode($response, true);
    }
}
