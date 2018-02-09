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

class WeChatBase extends Component
{

	public $_user;

	private $_app;

	private $_miniApp;

	private $_payment;

	/**
	 * @var array 配置信息
	 */
	public $config;

	public function init()
	{
		parent::init();

		$this->config = isset(Yii::$app->params['wechat_config']) ? Yii::$app->params['wechat_config'] : null;
	}


	public function getApp()
	{
		if (!$this->_app instanceof \EasyWeChat\OfficialAccount\Application) {
			$this->_app = Factory::officialAccount($this->config);
		}

		return $this->_app;
	}

	public function getMiniApp()
	{
		if (!$this->_miniApp instanceof \EasyWeChat\MiniProgram\Application) {
			$this->_miniApp = Factory::miniProgram($this->config);
		}

		return $this->_miniApp;
	}

	public function getPayment()
	{

		if (!$this->_payment instanceof \EasyWeChat\Payment\Application) {
			$this->_payment = Factory::payment($this->config);
		}

		return $this->_payment;
	}

}