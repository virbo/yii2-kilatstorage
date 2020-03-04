Library for S3 Kilatstorage
===========================
Library for S3 Kilatstorage

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require virbo/yii2-kilatstorage "~1.0"
```

or add

```
"virbo/yii2-kilatstorage": "~1.0"
```

to the require section of your `composer.json` file.


Usage
-----
Add this configuration to your config file

```php
 'components' => [
    ...
    's3' => [
        'class' => 'virbo\kilatstorage\S3Client',
            'credentials' => [
                'key' => 'kilatstorage-key',
                'secret' => 'kilatstorage-secret',
            ],
            'region' => 'kilatstorage-region', //default: 'id-jkt-1
            'version' => 'kilatstorage-version', //default: 'latest'
        ],
    ],
    ...
```

and then create new function, example:

#Create bucket
~~~php
public function actionCreate()
{
    $s3 = Yii::$app->s3;
    try {
        $result = $s3->createBucket('new_bucket_name');
        return $result;
    } catch (S3Exception $e) {
        echo $e->getMessage();
    }
}
~~~

#List Bucket
~~~php
public function actionListBucket()
{
    $s3 = Yii::$app->s3;
    try {
    	$result = $s3->listBuckets();
    	foreach ($result['Buckets'] as $bucket) {
    	    echo $bucket['Name'] . "\n";
    	}
    } catch (S3Exception $e) {
    	echo $e->getMessage();
    }
}
~~~

#List Object/Content
~~~php
public function actionList()
{
    $s3 = Yii::$app->s3;
    try {
        $result = $s3->listObjects('bucket_name');
        foreach ($result['Contents'] as $bucket) {
            echo $bucket['Key'] . "<br>";
        }
    } catch (S3Exception $e) {
        echo $e->getMessage();
    }
}
~~~

#Delete empty bucket
~~~php
public function actionDelete()
{
    $s3 = Yii::$app->s3;
    try {
        $result = $s3->deleteBucket('bucket_name');
        return $result;
    } catch (S3Exception $e) {
        echo $e->getMessage();
    }
}
~~~

#Upload object/content
~~~php
public function actionUpload()
{
    $s3 = Yii::$app->s3;
    $file = Yii::getAlias('@web/assets/images/image1.jpg');
    $key = 'assets/images/'.basename($file);     //will put object in folder assets/images
    
    return $s3->putObject('marketplace', $key, $file);
}
~~~

