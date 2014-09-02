<?php

/**
* 这是一个 调用 api.flvxz.com 网页视频解析 类
* 精简返回的数据，只获取清晰度最高的下载链接
* @read 		http://www.xuyangjie.cn/the-study/getWebVideoRealDown.html
* @GitHub		https://github.com/xuyangmiaomiao/getWebVideoRealDown
* @email		xuyangmiaomiao@gmail.com
* @webSite 		http://www.xuyangjie.cn/
* @time 		2014年9月2日 19:58:52
* @author		许杨淼淼
*/
error_reporting(0);
	class getWebVideoRealDown{
	
		/**
		* 这是一个对视频播放页面地址 进行按需求进行字符串处理和编码的函数
		* $url 视频播放页面地址
		* return 编码后的字符串
		*/
		static private function encodeUrl($url){
		
			$encodeUrl = strtr(base64_encode(str_replace('://', ':##', $url)), '+/', '-_');
			return $encodeUrl;
		}
		
		/**
		* 这是一个构建请求地址的函数
		* $encodeUrl 编码后的字符串
		* return 请求地址
		*/
		static private function createRequestUrl($encodeUrl){
			
			$requestUrl = 'http://api.flvxz.com/hd/2/jsonp/purejson/url/' . $encodeUrl;
			return $requestUrl;
		}
		
		/**
		* 这是一个获取原生JSON格式数据的函数
		* $requestUrl json格式数据 请求地址
		* return JSON格式数据
		*/
		static private function getJsonData($requestUrl){
		
			if(!is_null($requestUrl)){
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $requestUrl);
				curl_setopt($ch, CURLOPT_REFERER, 'http://www.flvxz.com');
				curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:24.0) Gecko/20100101 Firefox/24.0');
				$jsonData = curl_exec($ch);
				curl_close($ch);
				return $jsonData;
			}
		}
		
		/**
		* 这是一个精简原生JSON格式数据的函数
		* $jsonData json格式原生数据
		* return JSON格式数据
		*/
		static private function getLiteJsonData($jsonData){
		
			$liteData = json_decode($jsonData, true);
			$liteArrayData = array();
			$liteArrayData['title'] = $liteData['0']['title'];
			$liteArrayData['url'] = $liteData['0']['files']['0']['furl'];
			$liteArrayData['type'] = $liteData['0']['files']['0']['ftype'];
			$liteArrayData['time'] = $liteData['0']['files']['0']['time'];
			$liteArrayData['size'] = $liteData['0']['files']['0']['size'];
			$liteJsonData = json_encode($liteArrayData);
			return $liteJsonData;
		}
		
		
		/**
		* 唯一入口函数
		*/
		static public function index($url){
		
			if(is_string($url)){
				if(strlen($url) > 10){
					$encodeUrl = self::encodeUrl($url);
					$requestUrl = self::createRequestUrl($encodeUrl);
					$jsonData = self::getJsonData($requestUrl);
					$liteJsonData = self::getLiteJsonData($jsonData);
					return $liteJsonData;
				}
			}
			return json_encode(array('msg'=>'error','code'=>'400','content'=>'未输入地址'));
		}
	}

	isset($_GET['url']) ? $url = $_GET['url'] : die(json_encode(array('msg'=>'error','code'=>'400','content'=>'未输入地址')));
	echo getWebVideoRealDown::index($url);
?>