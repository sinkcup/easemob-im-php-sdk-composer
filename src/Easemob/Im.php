<?php
/**
 * 环信即时通讯云 easemob IM PHP SDK（composer版）
 *
 * 书写规范：PSR2
 *
 * phpcs --standard=PSR2 *.php
 *
 * @author   sink <sinkcup@live.it>
 * @link     http://docs.easemob.com/im/100serverintegration/20users
 */

namespace Easemob;

class Im
{
    private $conf = array(
        'api_root' => 'https://a1.easemob.com/',
        'client_id' => null,
        'client_secret' => null,
        'org_name' => null,
        'app_name' => null,
        'access_token' => null,
    );

    public function __construct($conf)
    {
        $this->setConf($conf);
        if (empty($this->conf['access_token'])) {
            $this->conf['access_token'] = $this->grantToken()['access_token'];
        }
    }

    public function setConf($conf)
    {
        $this->conf = array_merge($this->conf, $conf);
        $this->conf['api_of_app'] = $this->conf['api_root'] . $this->conf['org_name'] . '/' . $this->conf['app_name'];
        return true;
    }

    /**
     * 获取授权管理员token
     *
     * @example shell curl -X POST "https://a1.easemob.com/easemob-demo/chatdemoui/token" -d '{"grant_type":"client_credentials","client_id":"YXA6wDs-MARqEeSO0VcBzaqg11","client_secret":"YXA6JOMWlLap_YbI_ucz77j-4-mI0dd"}'
     * @return boolean
     */
    public function grantToken()
    {
        $data = [
            'grant_type' => 'client_credentials',
            'client_id' => $this->conf['client_id'],
            'client_secret' => $this->conf['client_secret'],
        ];
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $this->conf['api_of_app'] . '/token',
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
        );
        curl_setopt_array($ch, $options);
        $r = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($code == 200) {
            return json_decode($r, true);
        }
        throw new Exception($r, $code);
    }

    /**
     * 注册 IM 用户[单个]
     *
     * @example shell curl -X POST -i "https://a1.easemob.com/easemob-demo/chatdemoui/users" -d '{"username":"jliu","password":"123456"}'
     * @return boolean
     */
    public function register($username, $password, $nickname = null)
    {
        $data = [
            'username' => $username,
            'password' => $password,
        ];
        if (!empty($nickname)) {
            $data['nickname'] = $nickname;
        }

        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $this->conf['api_of_app'] . '/users',
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
        );
        if (!empty($this->conf['access_token'])) {
            $options[CURLOPT_HTTPHEADER][] = 'Authorization: Bearer ' . $this->conf['access_token'];
        }
        curl_setopt_array($ch, $options);
        $r = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($code == 200) {
            return json_decode($r, true);
        }
        throw new Exception($r, $code);
    }

    /**
     * 给 IM 用户添加好友
     *
     * @example curl -X POST -H "Authorization: Bearer YWMtP_8IisA-EeK-a5cNq4Jt3QAAAT7fI10IbPuKdRxUTjA9CNiZMnQIgk0LEU2" -i  "https://a1.easemob.com/easemob-demo/chatdemoui/users/jliu/contacts/users/yantao"
     * @return boolean
     */
    public function addFriend($ownerUsername, $friendUsername)
    {
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $this->conf['api_of_app'] . '/users/' . $ownerUsername . '/contacts/users/' . $friendUsername,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
        );
        $options[CURLOPT_HTTPHEADER][] = 'Authorization: Bearer ' . $this->conf['access_token'];
        curl_setopt_array($ch, $options);
        $r = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($code == 200) {
            return json_decode($r, true);
        }
        throw new Exception($r, $code);
    }

    /**
     * 发送文本消息
     *
     * @example curl -X POST -i -H "Authorization: Bearer YWMtxc6K0L1aEeKf9LWFzT9xEAAAAT7MNR_9OcNq-GwPsKwj_TruuxZfFSC2eIQ" "https://a1.easemob.com/easemob-demo/chatdemoui/messages" -d '{"target_type" : "users","target" : ["stliu1", "jma3", "stliu", "jma4"],"msg" : {"type" : "txt","msg" : "hello from rest"},"from" : "jma2"}'
     * @return boolean
     */
    public function sendMsg($target, $msg, $from, $targetType = 'users')
    {
        $data = [
            'target_type' => $targetType,
            'target' => $target,
            'msg' => $msg,
            'from' => $from,
        ];
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $this->conf['api_of_app'] . '/messages',
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
        );
        $options[CURLOPT_HTTPHEADER][] = 'Authorization: Bearer ' . $this->conf['access_token'];
        curl_setopt_array($ch, $options);
        $r = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($code == 200) {
            return json_decode($r, true);
        }
        throw new Exception($r, $code);
    }

    /**
     * 获取聊天记录
     *
     * @example curl -X GET -i -H "Authorization: Bearer YWMtxc6K0L1aEeKf9LWFzT9xEAAAAT7MNR_9OcNq-GwPsKwj_TruuxZfFSC2eIQ" "https://a1.easemob.com/easemob-demo/chatdemoui/chatmessages"
     * @return boolean
     */
    public function getMsgs($ql = null, $limit = null, $cursor = null)
    {
        $data = [];
        if (!empty($ql)) {
            $data['ql'] = $ql;
        }
        if (!empty($limit)) {
            $data['limit'] = $limit;
        }
        if (!empty($cursor)) {
            $data['cursor'] = $cursor;
        }
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $this->conf['api_of_app'] . '/chatmessages?' . http_build_query($data),
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
        );
        $options[CURLOPT_HTTPHEADER][] = 'Authorization: Bearer ' . $this->conf['access_token'];
        curl_setopt_array($ch, $options);
        $r = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($code == 200) {
            return json_decode($r, true);
        }
        throw new Exception($r, $code);
    }
}
