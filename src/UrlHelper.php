<?php
namespace kz\yii2_canurl;

use yii\base\InvalidArgumentException;


class UrlHelper {

    static public function get_current_url() {
        return \Yii::$app->getRequest()->getAbsoluteUrl();
    }

    static public function detect_url($url = null) {
        if (isset($url)) return $url;
        if (!is_string($url)) throw new InvalidArgumentException('(!is_string($url))');
        return static::get_current_url();
    }

    static public function get_parameters_from_url($url = null) {
        $url = static::detect_url($url);

        $parsed_url = parse_url($url);
        parse_str($parsed_url['query'], $parsed_url_query);
        return $parsed_url_query;
    }

    static public function add_parameters_to_url($url = null, $pname2value = []) {

        $url = static::detect_url($url);

        if (empty($pname2value)) return $url;
        if (!is_array($pname2value)) throw new InvalidArgumentException('(!is_array($pname2value))');

        $parsed_url = parse_url($url);

        if (empty($parsed_url['query'])) $parsed_url_query = [];
        else parse_str($parsed_url['query'], $parsed_url_query);

        foreach ($pname2value as $k => $v) {
            if (!isset($v)) unset($parsed_url_query[$k]);
            else $parsed_url_query[$k] = $v;
        }

        $parsed_url['query'] = self::build_query($parsed_url_query);

        $url = self::build_url($parsed_url);

        return $url;
    }

    static public function remove_parameters_from_url($url = null, $pnames = []) {

        $url = static::detect_url($url);

        if (empty($pnames)) return $url;
        if (!is_array($pnames)) throw new InvalidArgumentException('(!is_array($pnames))');

        $pname2value = array_fill_keys($pnames, null);

        return static::add_parameters_to_url($url, $pname2value);
    }

    static public function build_query($data, $glue = null, $use_rawurlencode = false) {

        if (empty($data)) return '';

        if (!is_array($data)) throw new InvalidArgumentException('(!is_array($data))');

        if (!isset($glue)) $glue = '&';
        if (!is_string($glue)) throw new InvalidArgumentException('(!is_string($glue))');

        if (!is_bool($use_rawurlencode)) throw new InvalidArgumentException('(!is_array($use_rawurlencode))');


        foreach ($data as $kkk => $vvv) {
            if (!isset($vvv)) unset($data[$kkk]);
        }

        if ($use_rawurlencode) {

            //$ret[] = rawurlencode($k).'='.rawurlencode($v);

            // echo rawurlencode('test test'); // 'test%20text'
            // echo urlencode('test test'); // 'test+text'

            // rawurlencode - URL-кодирование в соответствии с RFC3986, а
            // http_build_query по-умолчанию использует PHP_QUERY_RFC1738, поэтому
            // обязательно нужно явно указать тип кодирования PHP_QUERY_RFC3986
            return http_build_query($data, null, $glue, PHP_QUERY_RFC3986);

        }
        else {

            // $ret[] = urlencode($k).'='.urlencode($v);

            // Так называемое "обычное" кодирование данных, оно используется и при кодировании POST данных в формате application/x-www-form-urlencoded.
            // Это отличается от RFC3986-кодирования (см. rawurlencode()) тем, что, по историческим соображениям, пробелы кодируются как плюсы (+).
            // Эта функция удобна при кодировании строки для использования в части запроса URL для передачи переменных на следующую страницу.

            // echo rawurlencode('test test'); // 'test%20text'
            // echo urlencode('test test'); // 'test+text'

            // urlencode - URL-кодирование в соответствии с RFC1738, при этом
            // http_build_query по-умолчанию использует его же(PHP_QUERY_RFC1738), но для
            // более явного выделения типа кодирование указываем его явно PHP_QUERY_RFC1738
            return http_build_query($data, null, $glue, PHP_QUERY_RFC1738);

        }
    }


    static public function build_url($parsed_url) {

        if (!is_array($parsed_url)) throw new InvalidArgumentException('(!is_array($parsed_url))');

        $href = '';
        if (!empty($parsed_url['host'])) {
            if (!empty($parsed_url['scheme'])) $href .= $parsed_url['scheme'].'://';
            if (!empty($parsed_url['user'])) {
                $href .= $parsed_url['user'];
                if (!empty($parsed_url['pass'])) $href .= ':'.$parsed_url['pass'];
                $href .= '@';
            }
            $href .= $parsed_url['host'];
            if (!empty($parsed_url['port'])) $href .= ':'.$parsed_url['port'];
        }

        if (empty($parsed_url['path']) AND (!empty($parsed_url['query']) OR !empty($parsed_url['fragment']))) $parsed_url['path'] = '/';
        if (!empty($parsed_url['path']) AND ($parsed_url['path'] === '/') AND empty($parsed_url['query']) AND empty($parsed_url['fragment'])) $parsed_url['path'] = '';

        if (!empty($parsed_url['path'])) $href .= $parsed_url['path'];
        if (!empty($parsed_url['query'])) $href .= '?'.$parsed_url['query'];
        if (!empty($parsed_url['fragment'])) $href .= '#'.$parsed_url['fragment'];

        return $href;
    }
    static public function update_url($url = null, $for_update = []) {

        $url = static::detect_url($url);

        if (empty($for_update)) return $url;
        if (!is_array($for_update)) throw new InvalidArgumentException('(!is_array($for_update))');

        $parsed_url = parse_url($url);

        foreach ($for_update as $kkk => $vvv) {
            if ($kkk === 'params') $kkk = 'query';
            if (($kkk === 'query') AND is_array($vvv)) {

                $parsed_url_query = [];
                if (!empty($parsed_url['query'])) parse_str($parsed_url['query'], $parsed_url_query);

                foreach ($vvv as $qk => $qv) {
                    if (!isset($qv)) unset($parsed_url_query[$qk]);
                    else $parsed_url_query[$qk] = $qv;
                }

                $parsed_url['query'] = self::build_query($parsed_url_query);

                continue;
            }
            $parsed_url[$kkk] = $vvv;
        }

        $url = self::build_url($parsed_url);

        return $url;
    }





}