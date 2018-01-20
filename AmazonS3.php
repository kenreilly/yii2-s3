<?php
/**
 * @author: Jovani F. Alferez <vojalf@gmail.com>
 */

namespace kenreilly\yii2s3;

/**
 * A Yii2-compatible component wrapper for Aws\S3\S3Client.
 * Just add this component to your configuration providing this class,
 * key, secret and bucket.
 *
 * ~~~
 * 'components' => [
 *     'storage' => [
 *          'class' => '\jovanialferez\yii2s3\AmazonS3',
 *          'key' => 'AWS_ACCESS_KEY_ID',
 *          'secret' => 'AWS_SECRET_ACCESS_KEY',
 *          'bucket' => 'YOUR_BUCKET',
 *     ],
 * ],
 * ~~~
 *
 * You can then start using this component as:
 *
 * ```php
 * $storage = \Yii::$app->storage;
 * $url = $storage->uploadFile('/path/to/file', 'unique_file_name');
 * ```
 */
class AmazonS3 extends \yii\base\Component {

    public $key;
    public $secret;
    public $region;
    public $bucket;

    protected $_client;

    public function init() {

        parent::init();

        $this->_client = \Aws\S3\S3Client::factory([
            'key' => $this->key,
            'secret' => $this->secret,
            'region' => $this->region,
            'version' => 'latest'
        ]);
    }

    /**
     * Upload a file to S3
     *
     * @param string $path Full path of the file. Can be from tmp file path.
     * @param string $name Filename to save this file into S3. May include directories.
     * @return bool|string The S3 generated url that is publicly-accessible.
     */
    public function uploadFile(string $path, string $name) {

        try {

            $result = $this->_client->putObject([
                'ACL' => 'public-read',
                'Bucket' => $this->bucket,
                'Key' => $name,
                'SourceFile' => $path,
                'ContentType' => \yii\helpers\FileHelper::getMimeType($path),
            ]);

            return $result->get('ObjectURL');

        } catch (\Exception $e) { return false; }
    }

    /**
     * Upload a string of data to S3
     *
     * @param string $name Filename to save this file into S3. May include directories.
     * @param string $content_type Content-Type to save the file data as
     * @param string $data Data to upload to S3
     * @return bool|string The S3 generated url that is publicly-accessible.
     */
    public function uploadData(string $name, string $content_type, string $data) {

        try {

            $result = $this->_client->putObject([
                'ACL' => 'public-read',
                'Bucket' => $this->bucket,
                'Key' => $name,
                'Body' => $data,
                'ContentType' => $content_type
            ]);

            return $result->get('ObjectURL');

        } catch (\Exception $e) { return false; }
    }

    /**
     * Upload a string of data to S3
     *
     * @param string $name Filename to save this file into S3. May include directories.
     * @param string $content_type Content-Type to save the file data as
     * @param string $data Data to upload to S3
     * @return bool|string The S3 generated url that is publicly-accessible.
     */
    public function deleteObject(string $name) {

        try {

            $result = $this->_client->deleteObject([
                'Bucket' => $this->bucket,
                'Key' => $name
            ]);

            return $result->get('ObjectURL');

        } catch (\Exception $e) { return false; }
    }
}