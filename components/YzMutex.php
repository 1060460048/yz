<?php
/**
 * Class YzMutex provides a locking mechanism with timeout functionality
 * Based on the code of "mutex" extension (author: Y!!). This class was made fully static.
 * @author Y!!
 * @author Pavel Agalecky <pavel.agalecky@gmail.com>
 * @link http://www.yiiframework.com/extension/mutex/
 */
class YzMutex
{
    protected static $_locks = array();

    /**
     * @param string $id
     * @param int $timeout in seconds
     * @return bool
     */
    public static function lock($id, $timeout = 0)
    {

        $lockFileHandle = self::_getLockFileHandle($id);

        if (flock($lockFileHandle, LOCK_EX))
        {

            $data = @unserialize(@file_get_contents(self::_getMutexFile()));

            if (empty($data))
            {
                $data = array();
            }

            if (!isset($data[$id]) || ($data[$id][0] > 0 && $data[$id][0] + $data[$id][1] <= microtime(true)))
            {

                $data[$id] = array($timeout, microtime(true));

                array_push(self::$_locks, $id);

                $result = (bool)file_put_contents(self::_getMutexFile(), serialize($data));

            }

        }

        fclose($lockFileHandle);

        @unlink(self::_getLockFile($id));

        return isset($result) ? $result : false;

    }

    /**
     * @param string $id
     * @return bool
     * @throws CException
     */
    public static function unlock($id = null)
    {

        if (null === $id && null === ($id = array_pop(self::$_locks)))
        {
            throw new CException("No lock available that could be released. Make sure to setup a lock first.");
        }
        elseif (in_array($id, self::$_locks))
        {
            throw new CException("Don't define the id for a local lock. Only do it for locks that weren't created within the current request.");
        }

        $lockFileHandle = self::_getLockFileHandle($id);

        if (flock($lockFileHandle, LOCK_EX))
        {
            $data = @unserialize(@file_get_contents(self::_getMutexFile()));

            if (!empty($data) && isset($data[$id]))
            {

                unset($data[$id]);

                $result = (bool)file_put_contents(self::_getMutexFile(), serialize($data));

            }

        }

        fclose($lockFileHandle);

        @unlink(self::_getLockFile($id));

        return isset($result) ? $result : false;

    }

    protected function _getMutexFile()
    {
        return Yii::app()->getRuntimePath() . '/mutex.bin';
    }

    protected static function _getLockFile($id)
    {
        return self::_getMutexFile() . '.' . md5($id) . '.lock';
    }

    protected static function _getLockFileHandle($id)
    {
        return fopen(self::_getLockFile($id), 'a+b');
    }
}