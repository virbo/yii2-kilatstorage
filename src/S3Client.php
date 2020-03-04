<?php

namespace virbo\kilatstorage\src;

use Aws\S3\Exception\S3Exception;
use yii\base\Component;

/**
 * Component S3 for kilat storage (using AWS SDK)
 * Reference from https://github.com/CloudKilat/kilatstorage-with-amazon-sdk
 *
 * @author Yusuf Ayuba <yusuf@dutainformasi.net>
 *
 * for example configuration
 * ```
 * 'components' => [
 *      's3' => [
 *          'class' => 'virbo\kilatstorage\S3Client',
 *          'credentials' => [
 *              'key' => 'kilatstorage-key',
 *              'secret' => 'kilatstorage-secret',
 *          ],
 *          'region' => 'kilatstorage-region', //default: 'id-jkt-1
 *          'version' => 'kilatstorage-version', //default: 'latest'
 *      ],
 * ],
 * ```
 */
class S3Client extends Component
{
    /*
     * @var string default kilatstorage version
     * */
    public $version = 'latest';

    /*
     * @var string default kilatstorage region
     * */
    public $region = 'id-jkt-1';

    /*
     * @var string default acl
     * list ACL S3
     * ---
     * private,
     * public-read,
     * public-read-write,
     * aws-exec-read,
     * authenticated-read,
     * bucket-owner-read,
     * bucket-owner-full-control,
     * log-delivery-write
     *
     * for more informations https://docs.aws.amazon.com/AmazonS3/latest/dev/acl-overview.html
     * */
    public $acl = 'public-read';

    /*
     * @var string default kilatstorage endpoint
     * */
    public $endpoint = 'https://s3-id-jkt-1.kilatstorage.id/';

    /*
     * @var array specifies the AWS credentials
     * ---
     * 'credentials' => [
     *      'key'    => 'access_key',
     *      'secret' => 'secret_key',
     * ],
     * */
    public $credentials = [];

    protected $_s3;

    public function init()
    {
        $this->_s3 = new \Aws\S3\S3Client([
            'version' => $this->version,
            'region'  => $this->region,
            'credentials' => $this->credentials,
            'endpoint' => $this->endpoint
        ]);
    }
    /*
     * listBucket method
     * return array
     *
     * Example use
     * ~~~
     * public function actionListBucket()
     * {
     *      $s3 = Yii::$app->s3;
     *      try {
     *          $result = $s3->listBuckets();
     *          foreach ($result['Buckets'] as $bucket) {
     *              echo $bucket['Name'] . "\n";
     *          }
     *      } catch (S3Exception $e) {
     *          echo $e->getMessage();
     *      }
     * }
     * ~~~
     * */
    public function listBuckets()
    {
        return $this->_s3->listBuckets();
    }
    /*
     * listObject method
     * return array
     *
     * Example use
     * ```
     * public function actionList()
     * {
     *      $s3 = Yii::$app->s3;
     *      try {
     *          $result = $s3->listObjects('marketplace');
     *          foreach ($result['Contents'] as $bucket) {
     *              echo $bucket['Key'] . "<br>";
     *          }
     *      } catch (S3Exception $e) {
     *          echo $e->getMessage();
     *      }
     * }
     * ```
     * */
    public function listObjects($bucket)
    {
        return $this->_s3->listObjects(['Bucket' => $bucket]);
    }
    /*
     * createBucket method
     * return array
     *
     * Example use
     * ~~~
     * public function actionCreate()
     * {
     *      $s3 = Yii::$app->s3;
     *      try {
     *          $result = $s3->createBucket('new_bucket_name');
     *          return $result;
     *      } catch (S3Exception $e) {
     *          echo $e->getMessage();
     *      }
     * }
     * ~~~
     * */
    public function createBucket($bucket)
    {
        return $this->_s3->createBucket(['Bucket' => $bucket]);
    }
    /*
     * deleteObject method
     * Delete empty bucket
     *
     * Example use
     * ~~~
     * public function actionDelete()
     * {
     *      $s3 = Yii::$app->s3;
     *      try {
     *          $result = $s3->deleteBucket('bucket_name');
     *          return $result;
     *      } catch (S3Exception $e) {
     *          echo $e->getMessage();
     *      }
     * }
     * ~~~
     * */
    public function deleteObject($bucket)
    {
        return $this->_s3->deleteObject([
            'Bucket' => $bucket,
            'Key' => $this->credentials['key']
        ]);
    }
    /*
     *
     * putObject method for put/upload object to S3 storage
     * return Array
     *
     * Example use
     * ```
     * public function actionUpload()
     * {
     *      $s3 = Yii::$app->s3;
     *      $file = Yii::getAlias('@web/assets/images/image1.jpg');
     *      $key = 'assets/images/'.basename($file);     //will put object in folder assets/images
     *
     *      return $s3->putObject('marketplace', $key, $file);
     * }
     * ```
     * */
    public function putObject($bucket, $key, $source)
    {
        try {
            $result = $this->_s3->putObject([
                'Bucket' => $bucket,
                'Key' => $key,
                'SourceFile' => $source,
                'ACL' => $this->acl
            ]);

            return $result['@metadata'];
        } catch (S3Exception $e) {
            return $e->getMessage();
        }
    }
}