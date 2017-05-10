<?php
namespace SCRMHub\WordpressPlugin\NetworkCore;

use SCRMHub\WordpressPlugin\NetworkCore\_BaseSocialNetwork as BaseSocialNetwork;

class Wechat extends BaseSocialNetwork {
	protected $network = 'wechat';

	protected $networkLabel = 'WeChat';

    protected $assets_js = ['https://res.wx.qq.com/open/js/jweixin-1.0.0.js'];
}