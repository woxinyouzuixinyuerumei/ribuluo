<?php
/* *
 * 使用openssl实现RSA（非对称）加密
 * php需要openssl扩展支持
 * liangke.li@monph.com 
 */
namespace crypt;
 
class RSA{    
    private $public_key = ''; //公密钥
    private $private_key = ''; //私密钥
    private $public_key_resource = ''; //公密钥资源
    private $private_key_resource = ''; //私密钥资源
	
    /**
     * 架构函数
     */
    public function __construct($public_key,$private_key) {
			//赋值
           $this->public_key = $public_key;
		   $this->private_key = $private_key;
           $this->public_key_resource = $this->is_bad_public_key($this->public_key);
		   $this->private_key_resource = $this->is_bad_private_key($this->private_key);
    }
    private function is_bad_public_key($public_key) {
        return openssl_pkey_get_public($public_key);
    }
    private function is_bad_private_key($private_key) {
        return openssl_pkey_get_private($private_key);
    }
	
    /**
     * 生成一对公私密钥 成功返回 公私密钥数组 失败 返回 false
     */
    public function create_key($key_bits=1024) {
	   	$config = array(
            'private_key_bits'=>$key_bits,
			'config'=> dirname(__FILE__).'/openssl.cnf'
        );
        $rs = openssl_pkey_new($config);
        if($rs == false) return false;
        openssl_pkey_export($rs, $private_key, NULL, $config);
        $public_key = openssl_pkey_get_details($rs);
        return array('private_key'=>$private_key,'public_key'=>$public_key["key"]);
    }
	
    /**
     * 用私密钥加密
     */
    public function private_encrypt($input) {
	 	//openssl_private_encrypt($input,$output,$this->private_key_resource);
		$output  = '';
		for($i = 0; $i < strlen($input)/117; $i++  ) {
			$data = substr($input, $i * 117, 117);
			openssl_private_encrypt($data, $decrypt, $this->private_key_resource);
			$output .= $decrypt;
		}
        return base64_encode($output);
    }
	
    /**
     * 解密 私密钥加密后的密文
     */
    public function public_decrypt($input) {
        //openssl_public_decrypt(base64_decode($input),$output,$this->public_key_resource);
		$content = base64_decode($input);
		//把需要解密的内容，按128位拆开解密
		$output  = '';
		for($i = 0; $i < strlen($content)/128; $i++  ) {
			$data = substr($content, $i * 128, 128);
			openssl_public_decrypt($data, $decrypt, $this->public_key_resource);
			$output .= $decrypt;
		}
        return $output;
    }
	
	
    /**
     * 用公密钥加密
     */
    public function public_encrypt($input) {
        //openssl_public_encrypt($input,$output,$this->public_key_resource);
		$output  = '';
		for($i = 0; $i < strlen($input)/117; $i++  ) {
			$data = substr($input, $i * 117, 117);
			openssl_public_encrypt($data, $decrypt, $this->public_key_resource);
			$output .= $decrypt;
		}
        return base64_encode($output);
    }
	
    /**
     * 解密 公密钥加密后的密文
     */
    public function private_decrypt($input) {
        //openssl_private_decrypt(base64_decode($input),$output,$this->private_key_resource);
		$content = base64_decode($input);
		//把需要解密的内容，按128位拆开解密
		$output  = '';
		for($i = 0; $i < strlen($content)/128; $i++  ) {
			$data = substr($content, $i * 128, 128);
			openssl_private_decrypt($data, $decrypt, $this->private_key_resource);
			$output .= $decrypt;
		}
        return $output;
        return $output;
    }

    /**
     * 私钥签名
     */
    public function private_sign($data) {
        if(!is_string($data)){
            return null;
        }       
        openssl_sign($data, $sign, $this->private_key_resource);
        if($sign){
            return base64_encode($sign);
        }
        return null;
    }
    /**
     * 公钥验签
     */
    public function public_verify($data,$sign) {
        if(!is_string($data) || !is_string($sign)){
            return null;
        }
        $result = (bool)openssl_verify($data, base64_decode($sign), $this->public_key_resource);
        //openssl_free_key($this->_pubKey);
        return $result;
    }

    public function __destruct(){
        @ fclose($this->private_key_resource);
        @ fclose($this->public_key_resource);
    }
}
?>