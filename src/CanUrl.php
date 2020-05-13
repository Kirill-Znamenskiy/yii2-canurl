<?php
namespace KZ\yii2_canurl;


use Closure;
use yii\base\BootstrapInterface;
use yii\base\Component;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\base\InvalidArgumentException;
use yii\base\UserException;
use yii\helpers\ArrayHelper;
use yii\web\Application;
use yii\web\View;

/**
 * Class CanUrl
 *
 * @property string|array|Closure|null  scheme
 * @property string|array|Closure|null  schema
 *
 * @property string|array|Closure|null  user
 * @property string|array|Closure|null  login
 *
 * @property string|array|Closure|null  pass
 * @property string|array|Closure|null  password
 *
 * @property string|array|Closure|null  host
 * @property string|array|Closure|null  domain
 *
 * @property string|array|Closure|null  port
 *
 * @property string|array|Closure|null  path
 *
 * @property string|array|Closure|null  fragment
 * @property string|array|Closure|null  frag
 *
 * @property array|null   query_params
 * @property array|null   core_params
 *
 * @property string       canurl
 *
 *
 *
 *
 * @property array|null   important_params
 *
 * @property array|null   minor_params
 *
 * @property string       redirurl
 *
 */
class CanUrl extends Component implements BootstrapInterface {

    public function bootstrap($application) {
        if ($application instanceof Application) {
            $this->init_events();
        }
    }
    public function init_events() {
        \Yii::$app->getView()->on(View::EVENT_END_PAGE, [$this, 'event_end_page']);
        \Yii::$app->on(Application::EVENT_AFTER_REQUEST, [$this, 'event_after_request']);
    }





    protected $_scheme;
    public function SETscheme($value) { $this->_scheme = $value; return $this;  }
    public function GETscheme() { return $this->_scheme; }
    public function SETschema($value) { $this->_scheme = $value; return $this;  }
    public function GETschema() { return $this->_scheme; }

    protected $_user = '';
    public function SETuser($value) { $this->_user = $value; return $this;  }
    public function GETuser() { return $this->_user; }
    public function SETlogin($value) { $this->_user = $value; return $this;  }
    public function GETlogin() { return $this->_user; }

    protected $_pass = '';
    public function SETpass($value) { $this->_pass = $value; return $this;  }
    public function GETpass() { return $this->_pass; }
    public function SETpassword($value) { $this->_pass = $value; return $this;  }
    public function GETpassword() { return $this->_pass; }

    protected $_host;
    public function SEThost($value) { $this->_host = $value; return $this;  }
    public function GEThost() { return $this->_host; }
    public function SETdomain($value) { $this->_host = $value; return $this;  }
    public function GETdomain() { return $this->_host; }

    protected $_port = '';
    public function SETport($value) { $this->_port = $value; return $this;  }
    public function GETport() { return $this->_port; }

    protected $_path;
    public function SETpath($value) { $this->_path = $value; return $this;  }
    public function GETpath() { return $this->_path; }

    protected $_fragment = '';
    public function SETfragment($value) { $this->_fragment = $value; return $this; }
    public function GETfragment() { return $this->_fragment; }
    public function SETfrag($value) { $this->_fragment = $value; return $this; }
    public function GETfrag() { return $this->_fragment; }

    protected $_query_params;
    public function SETquery_params($query_params) {
        if (!(is_null($query_params) OR is_array($query_params))) throw new InvalidArgumentException('(!(is_null($query_params) OR is_array($query_params)))');
        $this->_query_params = $query_params;
        return $this;
    }
    public function GETquery_params() {
        if (!(is_null($this->_query_params) OR is_array($this->_query_params))) throw new InvalidArgumentException('(!(is_null($this->_query_params) OR is_array($this->_query_params)))');
        return $this->_query_params;
    }
    public function ADDquery_params($add_query_params) {
        if (empty($add_query_params)) $add_query_params = [];
        if (!is_array($add_query_params)) throw new InvalidArgumentException('(!is_array($add_query_params))');
        $this_query_params = $this->GETquery_params();
        if (!isset($this_query_params)) $this_query_params = $add_query_params;
        else $this_query_params = array_merge($this_query_params, $add_query_params);
        return $this->SETquery_params($this_query_params);
    }
    public function SETcore_params($core_params) { return $this->SETquery_params($core_params);  }
    public function GETcore_params() { return $this->GETquery_params(); }
    public function ADDcore_params($add_core_params) { return $this->ADDquery_params($add_core_params); }




    static public function get_parsed_current_url($current_url = null) {
        if (!isset($current_url)) $current_url = \Yii::$app->getRequest()->getAbsoluteUrl();
        if (!is_string($current_url) AND !is_array($current_url)) throw new InvalidArgumentException('(!is_string($current_url) AND !is_array($current_url))');
        elseif (is_string($current_url)) $parsed_current_url = parse_url($current_url);
        elseif (is_array($current_url)) $parsed_current_url = $current_url;
        else throw new UserException('!!!');
        return $parsed_current_url;
    }
    public function construct_parsed_new_url($is_final = true, $current_url = null) {
        if (!is_bool($is_final)) throw new InvalidArgumentException('(!is_bool($is_final))');
        $parsed_current_url = static::get_parsed_current_url($current_url);

        $parsed_new_url = [];

        $parsed_new_url['scheme'] = $this->make_item_value('scheme',$this->_scheme,$is_final,$parsed_current_url);
        $parsed_new_url['user'] = $this->make_item_value('user',$this->_user,$is_final,$parsed_current_url);
        $parsed_new_url['pass'] = $this->make_item_value('pass',$this->_pass,$is_final,$parsed_current_url);
        $parsed_new_url['host'] = $this->make_item_value('host',$this->_host,$is_final,$parsed_current_url);
        $parsed_new_url['port'] = $this->make_item_value('port',$this->_port,$is_final,$parsed_current_url);
        $parsed_new_url['path'] = $this->make_item_value('path',$this->_path,$is_final,$parsed_current_url);
        $parsed_new_url['fragment'] = $this->make_item_value('fragment',$this->_fragment,$is_final,$parsed_current_url);


        return $parsed_new_url;
    }
    public function make_item_value($item_name, $this_item_value, $is_required, $current_item_name2value) {
        $current_item_value = ArrayHelper::getValue($current_item_name2value, $item_name);
        if (!isset($this_item_value)) {
            if ($is_required) throw new InvalidConfigException('Value for "'.$item_name.'" is not set!');
            $rett = $current_item_value;
        }
        elseif ($this_item_value instanceof \Closure) {
            $rett = strval(call_user_func($this_item_value, $this, $item_name, $this_item_value, $is_required, $current_item_name2value, $current_item_value));
        }
        elseif (is_array($this_item_value)) {
            if (empty($this_item_value)) throw new InvalidConfigException('(is_array($this_elem_value)) AND (empty($this_elem_value)) $elem_name='.$item_name);
            if (!empty($current_item_value) AND in_array($current_item_value, $this_item_value, true)) {
                $rett = $current_item_value;
            }
            elseif (array_key_exists('_else_value_',$this_item_value)) {
                $rett = $this_item_value['_else_value_'];
            }
            else {
                $rett = reset($this_item_value);
            }
        }
        else {
            $rett = strval($this_item_value);
        }
        return $rett;
    }



    /** @return string */
    public function GETcanurl($current_url = null) {
        $parsed_current_url = static::get_parsed_current_url($current_url);


        $parsed_canurl = $this->construct_parsed_new_url(true, $parsed_current_url);

        if (!isset($this->_query_params)) throw new UserException('!isset($this->_query_params');
        else {

            $parsed_current_url_params = [];
            if (empty($parsed_current_url['query'])) $parsed_current_url_params = [];
            else parse_str($parsed_current_url['query'], $parsed_current_url_params);


            $parsed_canurl_params = [];

            if (!is_array($this->_query_params)) throw new UserException('(!is_array($this->_query_params))');
            $this_query_params = $this->_query_params;
            ksort($this_query_params);
            foreach ($this_query_params as $kkk => $vvv) {
                $parsed_canurl_params[$kkk] = $this->make_item_value($kkk,$vvv,true,$parsed_current_url_params);
            }

            $parsed_canurl['query'] = UrlHelper::build_query($parsed_canurl_params);
        }

        return UrlHelper::build_url($parsed_canurl);
    }

























    protected $_important_params;
    public function SETimportant_params($important_params) {
        if (!(is_null($important_params) OR is_array($important_params))) throw new InvalidArgumentException('(!(is_null($important_params) OR is_array($important_params)))');
        $this->_important_params = $important_params;
        return $this;
    }
    public function GETimportant_params() {
        if (!(is_null($this->_important_params) OR is_array($this->_important_params))) throw new InvalidArgumentException('(!(is_null($this->_important_params) OR is_array($this->_important_params)))');
        return $this->_important_params;
    }
    public function ADDimportant_params($add_important_params) {
        if (empty($add_important_params)) $add_important_params = [];
        if (!is_array($add_important_params)) throw new InvalidArgumentException('(!is_array($add_important_params))');
        $this_important_params = $this->GETimportant_params();
        if (!isset($this_important_params)) $this_important_params = $add_important_params;
        else $this_important_params = array_merge($this_important_params, $add_important_params);
        return $this->SETimportant_params($this_important_params);
    }
    public function SETimportant_pnames($important_pnames) {
        if (empty($important_pnames)) $important_pnames = [];
        if (is_string($important_pnames)) $important_pnames = [$important_pnames];
        if (!is_array($important_pnames)) throw new InvalidArgumentException('(!is_array($important_pnames))');
        $important_pnames = array_unique($important_pnames);
        $important_pnames = array_values($important_pnames);
        $important_params = array_fill_keys($important_pnames,null);
        return $this->SETimportant_params($important_params);
    }
    public function ADDimportant_pnames($add_important_pnames) {
        if (empty($add_important_pnames)) $add_important_pnames = [];
        if (is_string($add_important_pnames)) $add_important_pnames = [$add_important_pnames];
        if (!is_array($add_important_pnames)) throw new InvalidArgumentException('(!is_array($add_important_pnames))');
        $add_important_pnames = array_unique($add_important_pnames);
        $add_important_pnames = array_values($add_important_pnames);
        $add_important_params = array_fill_keys($add_important_pnames,null);
        return $this->ADDimportant_params($add_important_params);
    }
    public function ADDimportant_pname($add_important_pname) { return $this->ADDimportant_pnames($add_important_pname); }




    protected $_minor_params = [
        'from' => null,
        '_openstat' => null,
        'utm_source' => null,
        'utm_medium' => null,
        'utm_campaign' => null,
        'utm_content' => null,
        'utm_term' => null,
        'utm_referrer' => null,

        'pm_source' => null,
        'pm_block' => null,
        'pm_position' => null,


        'clid' => null,
        'yclid' => null,
        'ymclid' => null,
        'frommarket' => null,
        'text' => null,
    ];
    public function SETminor_params($minor_params) {
        if (!(is_null($minor_params) OR is_array($minor_params))) throw new InvalidArgumentException('(!(is_null($minor_params) OR is_array($minor_params)))');
        $this->_minor_params = $minor_params;
        return $this;
    }
    public function GETminor_params() {
        if (!(is_null($this->_minor_params) OR is_array($this->_minor_params))) throw new InvalidArgumentException('(!(is_null($this->_minor_params) OR is_array($this->_minor_params)))');
        return $this->_minor_params;
    }
    public function ADDminor_params($add_minor_params) {
        if (empty($add_minor_params)) $add_minor_params = [];
        if (!is_array($add_minor_params)) throw new InvalidArgumentException('(!is_array($add_minor_params))');
        $this_minor_params = $this->GETminor_params();
        if (!isset($this_minor_params)) $this_minor_params = $add_minor_params;
        else $this_minor_params = array_merge($this_minor_params, $add_minor_params);
        return $this->SETminor_params($this_minor_params);
    }
    public function SETminor_pnames($minor_pnames) {
        if (empty($minor_pnames)) $minor_pnames = [];
        if (is_string($minor_pnames)) $minor_pnames = [$minor_pnames];
        if (!is_array($minor_pnames)) throw new InvalidArgumentException('(!is_array($minor_pnames))');
        $minor_pnames = array_unique($minor_pnames);
        $minor_pnames = array_values($minor_pnames);
        $minor_params = array_fill_keys($minor_pnames,null);
        return $this->SETminor_params($minor_params);
    }
    public function ADDminor_pnames($add_minor_pnames) {
        if (empty($add_minor_pnames)) $add_minor_pnames = [];
        if (is_string($add_minor_pnames)) $add_minor_pnames = [$add_minor_pnames];
        if (!is_array($add_minor_pnames)) throw new InvalidArgumentException('(!is_array($add_minor_pnames))');
        $add_minor_pnames = array_unique($add_minor_pnames);
        $add_minor_pnames = array_values($add_minor_pnames);
        $add_minor_params = array_fill_keys($add_minor_pnames,null);
        return $this->ADDminor_params($add_minor_params);
    }
    public function ADDminor_pname($add_minor_pname) { return $this->ADDminor_pnames($add_minor_pname); }



    /**
     *
     * @param bool        $is_final
     * @param string|null $current_url
     * @return string
     */
    public function GETredirurl($is_final = true, $current_url = null) {
        if (!is_bool($is_final)) throw new InvalidArgumentException('(!is_bool($is_final))');
        $parsed_current_url = static::get_parsed_current_url($current_url);


        $parsed_redirurl = $this->construct_parsed_new_url($is_final, $parsed_current_url);



        if (!isset($this->_query_params,$this->_important_params,$this->_minor_params)) {
            if ($is_final) throw new UserException('!isset($this->_query_params,$this->_important_params,$this->_minor_params)');
            $parsed_redirurl['query'] = ArrayHelper::getValue($parsed_current_url, 'query');
        }
        else {

            $parsed_current_url_params = [];
            if (empty($parsed_current_url['query'])) $parsed_current_url_params = [];
            else parse_str($parsed_current_url['query'], $parsed_current_url_params);


            $parsed_redirurl_params = [];

            if (!is_array($this->_query_params)) throw new UserException('(!is_array($this->_query_params))');
            $this_query_params = $this->_query_params;
            ksort($this_query_params);
            foreach ($this_query_params as $kkk => $vvv) {
                $parsed_redirurl_params[$kkk] = $this->make_item_value($kkk,$vvv,true,$parsed_current_url_params);
            }

            if (!is_array($this->_important_params)) throw new UserException('(!is_array($this->_important_params))');
            $this_important_params = $this->_important_params;
            ksort($this_important_params);
            foreach ($this_important_params as $kkk => $vvv) {
                $parsed_redirurl_params[$kkk] = $this->make_item_value($kkk,$vvv,false,$parsed_current_url_params);
            }

            if (!is_array($this->_minor_params)) throw new UserException('(!is_array($this->_minor_params))');
            $this_minor_params = $this->_minor_params;
            ksort($this_minor_params);
            foreach ($this_minor_params as $kkk => $vvv) {
                $parsed_redirurl_params[$kkk] = $this->make_item_value($kkk,$vvv,false,$parsed_current_url_params);
            }

            $parsed_redirurl['query'] = UrlHelper::build_query($parsed_redirurl_params);
        }


        $redirurl = UrlHelper::build_url($parsed_redirurl);

        return $redirurl;
    }









    public $extra_tracked_methods = [];
    public $is_track_ajax = false;
    public $is_track_pjax = false;
    public $is_track_flash = false;


    public function is_tracked() {
        $error_handler = \Yii::$app->getErrorHandler();
        if (isset($error_handler->exception)) return false;

        $request = \Yii::$app->getRequest();

        $request_method = $request->getMethod();
        if (!in_array($request_method, ['GET','HEAD']) AND !in_array($request_method, $this->extra_tracked_methods)) return false;

        if (!$this->is_track_ajax AND $request->getIsAjax()) return false;
        if (!$this->is_track_pjax AND $request->getIsPjax()) return false;
        if (!$this->is_track_flash AND $request->getIsFlash()) return false;

        return true;
    }


    /**
     * @param bool        $is_final
     * @param string|null $current_url
     * @return bool|string
     */
    public function is_need_redirect($is_final, $current_url = null) {
        if (!is_bool($is_final)) throw new InvalidArgumentException('(!is_bool($throw_if_null))');
        if (!isset($current_url)) $current_url = \Yii::$app->getRequest()->getAbsoluteUrl();
        if (!is_string($current_url)) throw new InvalidArgumentException('(!is_string($current_url))');

        $parsed_current_url = parse_url($current_url);

        $redirurl = $this->GETredirurl($is_final, $parsed_current_url);

        if ($redirurl === $current_url) return false;

        $current_url = UrlHelper::build_url($parsed_current_url);

        if ($redirurl === $current_url) return false;

        if (empty($redirurl)) throw new UserException('(empty($redirurl))');

        return $redirurl;
    }


    /**
     * @param string|null $current_url
     * @param bool        $is_final
     */
    public function if_need_then_send_redirect($is_final, $current_url = null) {

        if (!$this->is_tracked()) return false;

        $response = \Yii::$app->getResponse();
        $response_status_code = $response->getStatusCode();

        if (intval($response_status_code) !== 200) return false;

        $res = $this->is_need_redirect($is_final, $current_url);
        if ($res === false) return false;

        $response->getHeaders()->set('X-Can-Url', 'YES');
        $response->getHeaders()->set('Location', $res);
        $response->setStatusCode(301);
        $response->send();
        exit;
    }


    public function event_end_page(Event $event) {

        if (!$this->is_tracked()) return false;

        $canurl = $this->GETcanurl();

        /** @var \yii\web\View $view */
        $view = $event->sender;
        $view->linkTags['canonical'] = '<link rel="canonical" href="'.$canurl.'"/>';

        return $this->if_need_then_send_redirect(TRUE);
    }

    public function event_after_request(Event $event) {
        return $this->if_need_then_send_redirect(TRUE);
    }


}