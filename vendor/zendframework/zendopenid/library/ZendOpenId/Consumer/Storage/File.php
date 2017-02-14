<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_OpenId
 */

namespace ZendOpenId\Consumer\Storage;

use Zend\Stdlib\ErrorHandler;
use ZendOpenId\Exception;

/**
 * External storage implemmentation using serialized files
 *
 * @category   Zend
 * @package    Zend_OpenId
 * @subpackage Zend_OpenId_Consumer
 */
class File extends AbstractStorage
{

    /**
     * Directory name to store data files in
     *
     * @var string $_dir
     */
    private $_dir;

    /**
     * Constructs storage object and creates storage directory
     *
     * @param string $dir directory name to store data files in
     * @throws OpenId\Exception\RuntimeException
     */
    public function __construct($dir = null)
    {
        if ($dir === null) {
            $tmp = getenv('TMP');
            if (empty($tmp)) {
                $tmp = getenv('TEMP');
                if (empty($tmp)) {
                    $tmp = "/tmp";
                }
            }
            $user = get_current_user();
            if (is_string($user) && !empty($user)) {
                $tmp .= '/' . $user;
            }
            $dir = $tmp . '/openid/consumer';
        }
        $this->_dir = $dir;
        if (!is_dir($this->_dir)) {
            if (!@mkdir($this->_dir, 0700, 1)) {
                throw new Exception\RuntimeException(
                    'Cannot access storage directory ' . $dir,
                    Exception\RuntimeException::ERROR_STORAGE);
            }
        }
        if (($f = fopen($this->_dir.'/assoc.lock', 'w+')) === null) {
            throw new Exception\RuntimeException(
                'Cannot create a lock file in the directory ' . $dir,
                Exception\RuntimeException::ERROR_STORAGE);
        }
        fclose($f);
        if (($f = fopen($this->_dir.'/discovery.lock', 'w+')) === null) {
            throw new Exception\RuntimeException(
                'Cannot create a lock file in the directory ' . $dir,
                Exception\RuntimeException::ERROR_STORAGE);
        }
        fclose($f);
        if (($f = fopen($this->_dir.'/nonce.lock', 'w+')) === null) {
            throw new Exception\RuntimeException(
                'Cannot create a lock file in the directory ' . $dir,
                Exception\RuntimeException::ERROR_STORAGE);
        }
        fclose($f);
    }

    /**
     * Stores information about association identified by $url/$handle
     *
     * @param string $url OpenID server URL
     * @param string $handle assiciation handle
     * @param string $macFunc HMAC function (sha1 or sha256)
     * @param string $secret shared secret
     * @param long $expires expiration UNIX time
     * @return bool
     */
    public function addAssociation($url, $handle, $macFunc, $secret, $expires)
    {
        $name1 = $this->_dir . '/assoc_url_' . md5($url);
        $name2 = $this->_dir . '/assoc_handle_' . md5($handle);
        ErrorHandler::start(E_WARNING);
        $lock = fopen($this->_dir . '/assoc.lock', 'w+');
        ErrorHandler::stop();
        if ($lock === false) {
            return false;
        }
        if (!flock($lock, LOCK_EX)) {
            fclose($lock);
            return false;
        }
        try {
            ErrorHandler::start(E_WARNING);
            $f = fopen($name1, 'w+');
            ErrorHandler::stop();
            if ($f === false) {
                fclose($lock);
                return false;
            }
            $data = serialize(array($url, $handle, $macFunc, $secret, $expires));
            fwrite($f, $data);
            if (function_exists('symlink')) {
                ErrorHandler::start(E_WARNING);
                unlink($name2);
                ErrorHandler::stop();
                if (symlink($name1, $name2)) {
                    fclose($f);
                    fclose($lock);
                    return true;
                }
            }
            ErrorHandler::start(E_WARNING);
            $f2 = fopen($name2, 'w+');
            ErrorHandler::stop();
            if ($f2) {
                fwrite($f2, $data);
                fclose($f2);
                ErrorHandler::start(E_WARNING);
                unlink($name1);
                ErrorHandler::stop();
                $ret = true;
            } else {
                $ret = false;
            }
            fclose($f);
            fclose($lock);
            return $ret;
        } catch (\Exception $e) {
            fclose($lock);
            throw $e;
        }
    }

    /**
     * Gets information about association identified by $url
     * Returns true if given association found and not expired and false
     * otherwise
     *
     * @param string $url OpenID server URL
     * @param string &$handle assiciation handle
     * @param string &$macFunc HMAC function (sha1 or sha256)
     * @param string &$secret shared secret
     * @param long &$expires expiration UNIX time
     * @return bool
     */
    public function getAssociation($url, &$handle, &$macFunc, &$secret, &$expires)
    {
        $name1 = $this->_dir . '/assoc_url_' . md5($url);
        ErrorHandler::start(E_WARNING);
        $lock = fopen($this->_dir . '/assoc.lock', 'w+');
        ErrorHandler::stop();
        if ($lock === false) {
            return false;
        }
        if (!flock($lock, LOCK_EX)) {
            fclose($lock);
            return false;
        }
        try {
            ErrorHandler::start(E_WARNING);
            $f = fopen($name1, 'r');
            ErrorHandler::stop();
            if ($f === false) {
                fclose($lock);
                return false;
            }
            $ret = false;
            $data = stream_get_contents($f);
            if (!empty($data)) {
                list($storedUrl, $handle, $macFunc, $secret, $expires) = unserialize($data);
                if ($url === $storedUrl && $expires > time()) {
                    $ret = true;
                } else {
                    $name2 = $this->_dir . '/assoc_handle_' . md5($handle);
                    fclose($f);
                    ErrorHandler::start(E_WARNING);
                    unlink($name2);
                    unlink($name1);
                    ErrorHandler::stop();
                    fclose($lock);
                    return false;
                }
            }
            fclose($f);
            fclose($lock);
            return $ret;
        } catch (\Exception $e) {
            fclose($lock);
            throw $e;
        }
    }

    /**
     * Gets information about association identified by $handle
     * Returns true if given association found and not expired and false
     * otherwise
     *
     * @param string $handle assiciation handle
     * @param string &$url OpenID server URL
     * @param string &$macFunc HMAC function (sha1 or sha256)
     * @param string &$secret shared secret
     * @param long &$expires expiration UNIX time
     * @return bool
     */
    public function getAssociationByHandle($handle, &$url, &$macFunc, &$secret, &$expires)
    {
        $name2 = $this->_dir . '/assoc_handle_' . md5($handle);
        ErrorHandler::start(E_WARNING);
        $lock = fopen($this->_dir . '/assoc.lock', 'w+');
        ErrorHandler::stop();
        if ($lock === false) {
            return false;
        }
        if (!flock($lock, LOCK_EX)) {
            fclose($lock);
            return false;
        }
        try {
            ErrorHandler::start(E_WARNING);
            $f = fopen($name2, 'r');
            ErrorHandler::stop();
            if ($f === false) {
                fclose($lock);
                return false;
            }
            $ret = false;
            $data = stream_get_contents($f);
            if (!empty($data)) {
                list($url, $storedHandle, $macFunc, $secret, $expires) = unserialize($data);
                if ($handle === $storedHandle && $expires > time()) {
                    $ret = true;
                } else {
                    fclose($f);
                    ErrorHandler::start(E_WARNING);
                    unlink($name2);
                    $name1 = $this->_dir . '/assoc_url_' . md5($url);
                    unlink($name1);
                    ErrorHandler::stop();
                    fclose($lock);
                    return false;
                }
            }
            fclose($f);
            fclose($lock);
            return $ret;
        } catch (\Exception $e) {
            fclose($lock);
            throw $e;
        }
    }

    /**
     * Deletes association identified by $url
     *
     * @param string $url OpenID server URL
     * @return bool
     */
    public function delAssociation($url)
    {
        $name1 = $this->_dir . '/assoc_url_' . md5($url);
        ErrorHandler::start(E_WARNING);
        $lock = fopen($this->_dir . '/assoc.lock', 'w+');
        ErrorHandler::stop();
        if ($lock === false) {
            return false;
        }
        if (!flock($lock, LOCK_EX)) {
            fclose($lock);
            return false;
        }
        try {
            ErrorHandler::start(E_WARNING);
            $f = fopen($name1, 'r');
            ErrorHandler::stop();
            if ($f === false) {
                fclose($lock);
                return false;
            }
            $data = stream_get_contents($f);
            if (!empty($data)) {
                list($storedUrl, $handle, $macFunc, $secret, $expires) = unserialize($data);
                if ($url === $storedUrl) {
                    $name2 = $this->_dir . '/assoc_handle_' . md5($handle);
                    fclose($f);
                    ErrorHandler::start(E_WARNING);
                    unlink($name2);
                    unlink($name1);
                    ErrorHandler::stop();
                    fclose($lock);
                    return true;
                }
            }
            fclose($f);
            fclose($lock);
            return true;
        } catch (\Exception $e) {
            fclose($lock);
            throw $e;
        }
    }

    /**
     * Stores information discovered from identity $id
     *
     * @param string $id identity
     * @param string $realId discovered real identity URL
     * @param string $server discovered OpenID server URL
     * @param float $version discovered OpenID protocol version
     * @param long $expires expiration UNIX time
     * @return bool
     */
    public function addDiscoveryInfo($id, $realId, $server, $version, $expires)
    {
        $name = $this->_dir . '/discovery_' . md5($id);
        ErrorHandler::start(E_WARNING);
        $lock = fopen($this->_dir . '/discovery.lock', 'w+');
        ErrorHandler::stop();
        if ($lock === false) {
            return false;
        }
        if (!flock($lock, LOCK_EX)) {
            fclose($lock);
            return false;
        }
        try {
            ErrorHandler::start(E_WARNING);
            $f = fopen($name, 'w+');
            ErrorHandler::stop();
            if ($f === false) {
                fclose($lock);
                return false;
            }
            $data = serialize(array($id, $realId, $server, $version, $expires));
            fwrite($f, $data);
            fclose($f);
            fclose($lock);
            return true;
        } catch (\Exception $e) {
            fclose($lock);
            throw $e;
        }
    }

    /**
     * Gets information discovered from identity $id
     * Returns true if such information exists and false otherwise
     *
     * @param string $id identity
     * @param string &$realId discovered real identity URL
     * @param string &$server discovered OpenID server URL
     * @param float &$version discovered OpenID protocol version
     * @param long &$expires expiration UNIX time
     * @return bool
     */
    public function getDiscoveryInfo($id, &$realId, &$server, &$version, &$expires)
    {
        $name = $this->_dir . '/discovery_' . md5($id);
        ErrorHandler::start(E_WARNING);
        $lock = fopen($this->_dir . '/discovery.lock', 'w+');
        ErrorHandler::stop();
        if ($lock === false) {
            return false;
        }
        if (!flock($lock, LOCK_EX)) {
            fclose($lock);
            return false;
        }
        try {
            ErrorHandler::start(E_WARNING);
            $f = fopen($name, 'r');
            ErrorHandler::stop();
            if ($f === false) {
                fclose($lock);
                return false;
            }
            $ret = false;
            $data = stream_get_contents($f);
            if (!empty($data)) {
                list($storedId, $realId, $server, $version, $expires) = unserialize($data);
                if ($id === $storedId && $expires > time()) {
                    $ret = true;
                } else {
                    fclose($f);
                    ErrorHandler::start(E_WARNING);
                    unlink($name);
                    ErrorHandler::stop();
                    fclose($lock);
                    return false;
                }
            }
            fclose($f);
            fclose($lock);
            return $ret;
        } catch (\Exception $e) {
            fclose($lock);
            throw $e;
        }
    }

    /**
     * Removes cached information discovered from identity $id
     *
     * @param string $id identity
     * @return bool
     */
    public function delDiscoveryInfo($id)
    {
        $name = $this->_dir . '/discovery_' . md5($id);
        ErrorHandler::start(E_WARNING);
        $lock = fopen($this->_dir . '/discovery.lock', 'w+');
        ErrorHandler::stop();
        if ($lock === false) {
            return false;
        }
        if (!flock($lock, LOCK_EX)) {
            fclose($lock);
            return false;
        }
        try {
            ErrorHandler::start(E_WARNING);
            unlink($name);
            ErrorHandler::stop();
            fclose($lock);
            return true;
        } catch (\Exception $e) {
            fclose($lock);
            throw $e;
        }
    }

    /**
     * The function checks the uniqueness of openid.response_nonce
     *
     * @param string $provider openid.openid_op_endpoint field from authentication response
     * @param  string $nonce openid.response_nonce field from authentication response
     * @return bool
     */
    public function isUniqueNonce($provider, $nonce)
    {
        $name = $this->_dir . '/nonce_' . md5($provider.';'.$nonce);
        ErrorHandler::start(E_WARNING);
        $lock = fopen($this->_dir . '/nonce.lock', 'w+');
        ErrorHandler::stop();
        if ($lock === false) {
            return false;
        }
        if (!flock($lock, LOCK_EX)) {
            fclose($lock);
            return false;
        }
        try {
            ErrorHandler::start(E_WARNING);
            $f = fopen($name, 'x');
            ErrorHandler::stop();
            if ($f === false) {
                fclose($lock);
                return false;
            }
            fwrite($f, $provider.';'.$nonce);
            fclose($f);
            fclose($lock);
            return true;
        } catch (\Exception $e) {
            fclose($lock);
            throw $e;
        }
    }

    /**
     * Removes data from the uniqueness database that is older then given date
     *
     * @param mixed $date date of expired data
     */
    public function purgeNonces($date=null)
    {
        ErrorHandler::start(E_WARNING);
        $lock = fopen($this->_dir . '/nonce.lock', 'w+');
        ErrorHandler::stop();
        if ($lock !== false) {
            flock($lock, LOCK_EX);
        }
        try {
            if (!is_int($date) && !is_string($date)) {
                foreach (glob($this->_dir . '/nonce_*') as $name) {
                    ErrorHandler::start(E_WARNING);
                    unlink($name);
                    ErrorHandler::stop();
                }
            } else {
                if (is_string($date)) {
                    $time = time($date);
                } else {
                    $time = $date;
                }
                foreach (glob($this->_dir . '/nonce_*') as $name) {
                    if (filemtime($name) < $time) {
                        ErrorHandler::start(E_WARNING);
                        unlink($name);
                        ErrorHandler::stop();
                    }
                }
            }
            if ($lock !== false) {
                fclose($lock);
            }
        } catch (\Exception $e) {
            if ($lock !== false) {
                fclose($lock);
            }
            throw $e;
        }
    }
}
