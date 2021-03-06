<?php
//
//    ______         ______           __         __         ______
//   /\  ___\       /\  ___\         /\_\       /\_\       /\  __ \
//   \/\  __\       \/\ \____        \/\_\      \/\_\      \/\ \_\ \
//    \/\_____\      \/\_____\     /\_\/\_\      \/\_\      \/\_\ \_\
//     \/_____/       \/_____/     \/__\/_/       \/_/       \/_/ /_/
//
//   上海商创网络科技有限公司
//
//  ---------------------------------------------------------------------------------
//
//   一、协议的许可和权利
//
//    1. 您可以在完全遵守本协议的基础上，将本软件应用于商业用途；
//    2. 您可以在协议规定的约束和限制范围内修改本产品源代码或界面风格以适应您的要求；
//    3. 您拥有使用本产品中的全部内容资料、商品信息及其他信息的所有权，并独立承担与其内容相关的
//       法律义务；
//    4. 获得商业授权之后，您可以将本软件应用于商业用途，自授权时刻起，在技术支持期限内拥有通过
//       指定的方式获得指定范围内的技术支持服务；
//
//   二、协议的约束和限制
//
//    1. 未获商业授权之前，禁止将本软件用于商业用途（包括但不限于企业法人经营的产品、经营性产品
//       以及以盈利为目的或实现盈利产品）；
//    2. 未获商业授权之前，禁止在本产品的整体或在任何部分基础上发展任何派生版本、修改版本或第三
//       方版本用于重新开发；
//    3. 如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回并承担相应法律责任；
//
//   三、有限担保和免责声明
//
//    1. 本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的；
//    2. 用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未获得商业授权之前，我们不承
//       诺提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任；
//    3. 上海商创网络科技有限公司不对使用本产品构建的商城中的内容信息承担责任，但在不侵犯用户隐
//       私信息的前提下，保留以任何方式获取用户信息及商品信息的权利；
//
//   有关本产品最终用户授权协议、商业授权与技术服务的详细内容，均由上海商创网络科技有限公司独家
//   提供。上海商创网络科技有限公司拥有在不事先通知的情况下，修改授权协议的权力，修改后的协议对
//   改变之日起的新授权用户生效。电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和
//   等同的法律效力。您一旦开始修改、安装或使用本产品，即被视为完全理解并接受本协议的各项条款，
//   在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本
//   授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。
//
//  ---------------------------------------------------------------------------------
//
defined('IN_ECJIA') or exit('No permission resources.');

/**
 * 店铺设置
 */
class admin_store_setting extends ecjia_admin {
	public function __construct() {
		parent::__construct();

		RC_Loader::load_app_func('global', 'store');
		RC_Loader::load_app_func('merchant_store', 'store');

		//全局JS和CSS
		RC_Script::enqueue_script('smoke');
		RC_Script::enqueue_script('bootstrap-placeholder');
		RC_Script::enqueue_script('jquery-validate');
		RC_Script::enqueue_script('jquery-form');
		RC_Script::enqueue_script('bootstrap-editable.min',RC_Uri::admin_url('statics/lib/x-editable/bootstrap-editable/js/bootstrap-editable.min.js'));
		RC_Style::enqueue_style('bootstrap-editable', RC_Uri::admin_url('statics/lib/x-editable/bootstrap-editable/css/bootstrap-editable.css'));
		RC_Script::enqueue_script('jquery-uniform');
		RC_Style::enqueue_style('uniform-aristo');
		RC_Script::enqueue_script('jquery-chosen');
		RC_Style::enqueue_style('chosen');

		RC_Style::enqueue_style('splashy');

        RC_Script::enqueue_script('jquery-range', RC_App::apps_url('statics/js/jquery.range.js', __FILE__), array(), false, 1);
        RC_Style::enqueue_style('range', RC_App::apps_url('statics/css/range.css', __FILE__), array());
        //时间控件
        RC_Script::enqueue_script('bootstrap-datepicker', RC_Uri::admin_url('statics/lib/datepicker/bootstrap-datepicker.min.js'));
        RC_Style::enqueue_style('datepicker', RC_Uri::admin_url('statics/lib/datepicker/datepicker.css'));

        RC_Script::enqueue_script('store', RC_App::apps_url('statics/js/admin_store_setting.js', __FILE__), array(), false, 1);
        RC_Script::enqueue_script('qq_map', ecjia_location_mapjs());

        RC_Script::localize_script('store', 'js_lang', config('app-merchant::jslang.merchant_page'));

		$store_id = intval($_GET['store_id']);
        $store_info = RC_DB::table('store_franchisee')->where('store_id', $store_id)->first();
        $nav_here = __('入驻商家', 'merchant');
        $url = RC_Uri::url('store/admin/join');
        if ($store_info['manage_mode'] == 'self') {
        	$nav_here = __('自营店铺', 'merchant');
        	$url = RC_Uri::url('store/admin/init');
        }
        ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here($nav_here, $url));
	}

	//店铺设置
	public function init() {
	    $this->admin_priv('store_set_manage');

        $store_id = intval($_GET['store_id']);
        if (empty($store_id)) {
            return $this->showmessage(__('请选择您要操作的店铺', 'merchant'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        }
        $store = RC_DB::table('store_franchisee')->where('store_id', $store_id)->first();
        
        if ($store['manage_mode'] == 'self') {
        	$this->assign('action_link', array('href' => RC_Uri::url('store/admin/init'), 'text' => __('自营店铺列表', 'merchant')));
        } else {
        	$this->assign('action_link', array('href' => RC_Uri::url('store/admin/join'), 'text' => __('入驻商家列表' ,'merchant')));
        }
        ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here($store['merchants_name'], RC_Uri::url('store/admin/preview', array('store_id' => $store_id))));
        ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('店铺设置', 'merchant')));
        
        ecjia_screen::get_current_screen()->set_sidebar_display(false);
        ecjia_screen::get_current_screen()->add_option('store_name', $store['merchants_name']);
        ecjia_screen::get_current_screen()->add_option('current_code', 'store_setting');
        
        $this->assign('ur_here', $store['merchants_name'].__(' - 店铺设置', 'merchant'));
        $this->assign('store_name', $store['merchants_name']);
        $menu = set_store_menu($store_id, 'store_set');

        $store_info = get_merchant_info($store_id, $arr);
        $this->assign('menu', $menu);
        $this->assign('store_info', $store_info);
	    return $this->display('store_setting.dwt');
	}

	//店铺设置修改
	public function edit() {
	    $this->admin_priv('store_set_update');
        $this->assign('action_link',array('href' => RC_Uri::url('merchant/admin_store_setting/init', array('store_id' => $_GET['store_id'])),'text' => __('店铺设置', 'merchant')));

        $store_id = intval($_GET['store_id']);
        $store = RC_DB::table('store_franchisee')->where('store_id', $store_id)->first();
        
        ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here($store['merchants_name'], RC_Uri::url('store/admin/preview', array('store_id' => $store_id))));
        ecjia_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('编辑入驻商', 'merchant')));
        
        ecjia_screen::get_current_screen()->set_sidebar_display(false);
        ecjia_screen::get_current_screen()->add_option('store_name', $store['merchants_name']);
        ecjia_screen::get_current_screen()->add_option('current_code', 'store_setting');
        
        $this->assign('store_name', $store['merchants_name']);
        $menu = set_store_menu($store_id, 'store_set');

        $store_info = get_merchant_info($store_id, $arr);
        $this->assign('menu', $menu);
        $this->assign('store_info', $store_info);
        $this->assign('form_action', RC_Uri::url('merchant/admin_store_setting/update'));
        $this->assign('ur_here', $store['merchants_name']. ' - ' .__('编辑入驻商' ,'merchant'));
        return $this->display('store_setting_edit.dwt');
	}

	//店铺设置修改
	public function update() {
	    $this->admin_priv('store_set_update', ecjia::MSGTYPE_JSON);

        $store_id = intval($_POST['store_id']);
        if(empty($store_id)){
            return $this->showmessage(__('参数错误', 'merchant'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        }

        $shop_kf_mobile         = empty($_POST['shop_kf_mobile'])	? '' : htmlspecialchars($_POST['shop_kf_mobile']);
        $shop_description       = empty($_POST['shop_description'])	? '' : htmlspecialchars($_POST['shop_description']);
        $shop_trade_time        = empty($_POST['shop_trade_time'])	? '' : htmlspecialchars($_POST['shop_trade_time']);
        $shop_notice            = empty($_POST['shop_notice'])		? '' : htmlspecialchars($_POST['shop_notice']);

        $merchant_config = array();

        // 店铺导航背景图
        if(!empty($_FILES['shop_nav_background']) && empty($_FILES['error']) && !empty($_FILES['shop_nav_background']['name'])){
        	$merchants_config['shop_nav_background'] = store_file_upload_info('shop_nav_background', '', $shop_nav_background, $store_id);
        }
        // 默认店铺页头部LOGO
        if(!empty($_FILES['shop_logo']) && empty($_FILES['error']) && !empty($_FILES['shop_logo']['name'])){
            $merchants_config['shop_logo'] = store_file_upload_info('shop_logo', '', $shop_logo, $store_id);
            
            //删除生成的店铺二维码
            $disk = RC_Filesystem::disk();
            $store_qrcode = 'data/qrcodes/merchants/merchant_'.$store_id.'.png';
            if ($disk->exists(RC_Upload::upload_path($store_qrcode))) {
            	$disk->delete(RC_Upload::upload_path().$store_qrcode);
            }
        }

        // APPbanner图
        if(!empty($_FILES['shop_banner_pic']) && empty($_FILES['error']) && !empty($_FILES['shop_banner_pic']['name'])){
            $merchants_config['shop_banner_pic'] = store_file_upload_info('shop_banner', 'shop_banner_pic', $shop_banner_pic, $store_id);
        }
        // 如果没有上传店铺LOGO 提示上传店铺LOGO
        $shop_logo = get_merchant_config($store_id, 'shop_logo');

        if(empty($shop_logo) && empty($merchants_config['shop_logo'])){
            return $this->showmessage(__('请上传店铺LOGO', 'merchant'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        }

        $merchants_config['shop_description'] = $shop_description;// 店铺描述
        $time = array();
        if(!empty($shop_trade_time)){
            $shop_time      = explode(',', $shop_trade_time);
            //营业时间验证
            if($shop_time[0] >= 1440) {
                return $this->showmessage(__('营业开始时间不能为次日', 'merchant'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
            }
            if($shop_time[1] - $shop_time[0] > 1440) {
                return $this->showmessage(__('营业时间最多为24小时', 'merchant'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
            }
            if(($shop_time[1] - $shop_time[0] == 1440) && ($shop_time[0] != 0)) {
                return $this->showmessage(__('24小时营业请选择0-24', 'merchant'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
            }
            $s_h = ($shop_time[0] / 60);
            $s_i = ($shop_time[0] % 60);
            $e_h = ($shop_time[1] / 60);
            $e_i = ($shop_time[1] % 60);
            $start_h        = empty($s_h)? '00' : intval($s_h);
            $start_i        = empty($s_i)? '00' : intval($s_i);
            $end_h          = empty($e_h)? '00' : intval($e_h);
            $end_i          = empty($e_i)? '00' : intval($e_i);
            $start_time     = $shop_time[0] == 0 ? '00:00' : $start_h.":".$start_i;
            $end_time       = $end_h.":".$end_i;
            $time['start']  = $start_time;
            $time['end']    = $end_time;
            $shop_trade_time = serialize($time);
            if($shop_trade_time != get_merchant_config($store_id, 'shop_trade_time')){
                $merchants_config['shop_trade_time'] = $shop_trade_time;// 营业时间
            }
        }
       	$merchants_config['shop_notice'] = $shop_notice;// 店铺公告
        $merchants_config['shop_kf_mobile'] = $shop_kf_mobile;// 客服电话
        
        if (!empty($merchants_config)) {
            $merchant = set_merchant_config($store_id, '', '', $merchants_config);
        } else {
            return $this->showmessage(__('您没有做任何修改', 'merchant'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_ERROR);
        }

        if (!empty($merchant)) {
        	$store_franchisee_db = RC_Model::model('store/orm_store_franchisee_model');
        	/* 释放app缓存*/
        	$store_cache_array = $store_franchisee_db->get_cache_item('store_list_cache_key_array');
        	if (!empty($store_cache_array)) {
        		foreach ($store_cache_array as $val) {
        			$store_franchisee_db->delete_cache_item($val);
        		}
        		$store_franchisee_db->delete_cache_item('store_list_cache_key_array');
        	}
            // 记录日志
            ecjia_admin::admin_log(__('修改店铺基本信息', 'merchant'), 'edit', 'merchant');
            return $this->showmessage(__('编辑成功', 'merchant'), ecjia::MSGTYPE_JSON | ecjia::MSGSTAT_SUCCESS, array('pjaxurl' => RC_Uri::url('merchant/admin_store_setting/edit', array('store_id' => $store_id))));
        }
	}
}

//end