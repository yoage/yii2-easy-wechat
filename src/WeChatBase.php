<?php
/**
 * @link http://www.yoage.com/
 * @copyright Copyright (c) 2011 Yoage Studio
 * @license http://www.yoage.com/license/
 */

namespace yoage\easywechat;

use yii\base\Component;
use EasyWeChat\Factory;
use Yii;

/**
 * Class WeChatBase
 * @package yoage\easywechat
 *
 * @property WeChatUser $user
 * @property  bool      isWechat
 */
class WeChatBase extends Component
{

	/**
	 * @var WeChatUser
	 */
	public static $_user;


	private $_app;

	private $_miniApp;

	private $_payment;

	/**
	 * @var array config information
	 */
	public $config;

	/**
	 * user identity class params
	 * @var array
	 */
	public $userOptions = [];
	/**
	 * wechat user info will be stored in session under this key
	 * @var string
	 */
	public $sessionParam = '_WeChatUser';
	/**
	 * returnUrl param stored in session
	 * @var string
	 */
	public $returnUrlParam = '_wechatReturnUrl';

	public function init()
	{
		parent::init();

		$this->config = isset(Yii::$app->params['wechat_config']) ? Yii::$app->params['wechat_config'] : null;
	}

	/**
	 * official account app
	 * @return \EasyWeChat\OfficialAccount\Application
	 */
	public function getApp()
	{
		if (!$this->_app instanceof \EasyWeChat\OfficialAccount\Application) {
			$this->_app = Factory::officialAccount($this->config);
		}

		return $this->_app;
	}

	/**
	 * mini program app
	 * @return \EasyWeChat\MiniProgram\Application
	 */
	public function getMiniApp()
	{
		if (!$this->_miniApp instanceof \EasyWeChat\MiniProgram\Application) {
			$this->_miniApp = Factory::miniProgram($this->config);
		}

		return $this->_miniApp;
	}

	/**
	 * this one for all payment
	 * @return \EasyWeChat\Payment\Application
	 */
	public function getPayment()
	{

		if (!$this->_payment instanceof \EasyWeChat\Payment\Application) {
			$this->_payment = Factory::payment($this->config);
		}

		return $this->_payment;
	}

	/**
	 * @return yii\web\Response
	 */
	public function authorizeRequired()
	{
		if (Yii::$app->request->get('code')) {

			// callback and authorize
			return $this->authorize($this->getApp()->oauth->user());
		} else {

			//setup the return url before for callback
			$this->setReturnUrl(Yii::$app->request->getUrl());

			// redirect to wechat authorize page
			return Yii::$app->response->redirect($this->getApp()->oauth->redirect()->getTargetUrl());

		}
	}

	/**
	 * @param \Overtrue\Socialite\User $user
	 * @return yii\web\Response
	 */
	public function authorize(\Overtrue\Socialite\User $user)
	{
		Yii::$app->session->set($this->sessionParam, $user->toJSON());

		return Yii::$app->response->redirect($this->getReturnUrl());
	}

	/**
	 * check if current user authorized
	 * @return bool
	 */
	public function isAuthorized()
	{
		$hasSession = Yii::$app->session->has($this->sessionParam);
		$sessionVal = Yii::$app->session->get($this->sessionParam);

		return ($hasSession && !empty($sessionVal));
	}

	/**
	 * @param string|array $url
	 */
	public function setReturnUrl($url)
	{
		Yii::$app->session->set($this->returnUrlParam, $url);
	}

	/**
	 * @param null $defaultUrl
	 * @return mixed|null|string
	 */
	public function getReturnUrl($defaultUrl = null)
	{
		$url = Yii::$app->getSession()->get($this->returnUrlParam, $defaultUrl);
		if (is_array($url)) {
			if (isset($url[0])) {
				return Yii::$app->getUrlManager()->createUrl($url);
			} else {
				$url = null;
			}
		}

		return $url === null ? Yii::$app->getHomeUrl() : $url;
	}

	/**
	 *
	 * @return bool|WeChatUser
	 */
	public function getUser()
	{
		if (!$this->isAuthorized()) {
			return false;
		}

		if (!self::$_user instanceof WeChatUser) {
			$userInfo    = Yii::$app->session->get($this->sessionParam);
			$config      = $userInfo ? json_decode($userInfo, true) : [];
			self::$_user = new WeChatUser($config);
		}

		return self::$_user;
	}

	/**
	 * overwrite the getter in order to be compatible with this component
	 * @param $name
	 * @return mixed
	 * @throws \Exception
	 */
	public function __get($name)
	{
		try {
			return parent::__get($name);
		}catch (\Exception $e) {
			if($this->getApp()->$name) {
				return $this->app->$name;
			}else{
				throw $e->getPrevious();
			}
		}
	}

	/**
	 * check if client is wechat
	 * @return bool
	 */
	public function getIsWechat()
	{
		return strpos($_SERVER["HTTP_USER_AGENT"], "MicroMessenger") !== false;
	}

}