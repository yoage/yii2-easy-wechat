<?php
/**
 * Project: yii2-easyWeChat.
 * Author: Max.wen
 * Date: <2016/05/10 - 17:17>
 */

namespace yoage\easywechat;


use yii\base\Component;

/**
 * Class WechatUser
 * @package common\components
 *
 * @property string $openId
 * @property string $unionId
 */
class WeChatUser extends Component
{
	/**
	 * @var string
	 */
	public $id;
	/**
	 * @var string
	 */
	public $nickname;
	/**
	 * @var string
	 */
	public $name;
	/**
	 * @var string
	 */
	public $email;
	/**
	 * @var string
	 */
	public $avatar;
	/**
	 * @var array
	 */
	public $original;
	/**
	 * @var \Overtrue\Socialite\AccessToken
	 */
	public $token;

	/**
	 * @return string
	 */
	public function getOpenId()
	{
		return isset($this->original['openid']) ? $this->original['openid'] : '';
	}

	/**
	 * @return string
	 */
	public function getUnionId()
	{
		return isset($this->original['unionid']) ? $this->original['unionid'] : '';
	}
}