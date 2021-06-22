<?php

require 'AWS/aws-autoloader.php';
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

/**
 * Amazon S3 Upload PHP class by Gautam Chawla
 * visit : https://github.com/gautam-chawla
 * @version 2.0
 */
class S3_bucket {

	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->config->load('s3', TRUE);
		$s3_config = $this->CI->config->item('s3');
		$this->bucket_name = $s3_config['bucket_name'];
		$this->folder_name = $s3_config['folder_name'];
		$this->s3_url = $s3_config['s3_url'];
		$this->access_key = $s3_config['access_key'];
		$this->secret = $s3_config['secret_key'];
        $this->region = $s3_config['region'];
		$this->version = $s3_config['version'];
        
	}

	function upload_file($file_path,$file_name)
	{
        $this->s3_key = "upload_models/".$file_name.".crv"; //replace file extention according to your file ex. .pdf, .jpeg, .png etc
		
		try {       
        $clientS3 = new Aws\S3\S3Client([
          'version' => $this->version,
          'region'  => $this->region,
          //'endpoint' => $this->s3_url,
          'credentials' => [
                    'key'    => $this->access_key,
                    'secret' => $this->secret,
              ],
        ]);

        // putObject method sends data to the chosen bucket (in our case, teste-marcelo)
        $response = $clientS3->putObject(array(
            'Bucket' => $this->bucket_name,
            'Key'    => $this->s3_key,
            'SourceFile' => $file_path,
        ));
            if($response['ObjectURL']){
                $res['url'] = $response['ObjectURL'];
                $res['s3_key'] = $this->s3_key;
                return $res;
            }
            
        } catch(Exception $e) {
            echo "Error > {$e->getMessage()}";
        }
        
	}
    
    function upload_allfile($file_path,$file_name,$folder,$ext)
	{
        $this->s3_key = $folder."/".$file_name.".".$ext;
		try {       
        $clientS3 = new Aws\S3\S3Client([
          'version' => $this->version,
          'region'  => $this->region,
          'credentials' => [
                    'key'    => $this->access_key,
                    'secret' => $this->secret,
              ],
        ]);

        // putObject method sends data to the chosen bucket (in our case, teste-marcelo)
        $response = $clientS3->putObject(array(
            'Bucket' => $this->bucket_name,
            'Key'    => $this->s3_key,
            'SourceFile' => $file_path,
        ));
            if($response['ObjectURL']){
                $res['url'] = $response['ObjectURL'];
                $res['s3_key'] = $this->s3_key;
                return $res;
            }
            
        } catch(Exception $e) {
            echo "Error > {$e->getMessage()}";
        }
        
	}
    
    function getFile($my_file_name,$original_file){
        $s3 = new S3Client([
            'credentials' => [ 
                    'key' => $this->access_key, 
                    'secret' => $this->secret 
                ],
            'version' => 'latest',
            'region'  => 'us-east-2'
        ]);

        try {
            // Get the object.
            $result = $s3->getObject([
                'Bucket' => $this->bucket_name,
                'Key'    => $my_file_name,
                'ResponseContentDisposition' => 'attachment; filename="'.$original_file.'"'
            ]);
           header('Content-Description: File Transfer');
            //this assumes content type is set when uploading the file.
            header('Content-Type: ' . $result->ContentType);
            header('Content-Disposition: attachment; filename=' . $original_file);
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            echo $result['Body'];
        } catch (S3Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }
    
	function getFilewithoutDownload($my_file_name,$original_file){
        $s3 = new S3Client([
            'credentials' => [ 
                    'key' => $this->access_key, 
                    'secret' => $this->secret 
                ],
            'version' => 'latest',
            'region'  => 'us-east-2'
        ]);

        try {
            //echo getcwd(); die;
            // Get the object.
            $result = $s3->getObject([
                'Bucket' => $this->bucket_name,
                'Key'    => $my_file_name,
				'SaveAs' => getcwd().'/assets/s3/'.$original_file
                
            ]);
          
           // echo $result;
        } catch (S3Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }
	
	public function getFileURL($my_file_name,$original_file){
        $s3 = new S3Client([
            'credentials' => [ 
                    'key' => $this->access_key, 
                    'secret' => $this->secret 
                ],
            'version' => 'latest',
            'region'  => 'us-east-2'
        ]);

		$url = $s3->getObjectUrl('panmerc-batch',$original_file);
		$cmd = $s3->getCommand('GetObject', [
			'Bucket' => $bucket,
			'Key' => $original_file
		]);
		
		$request = $s3->createPresignedRequest($cmd, '+20 minutes');
		$url = (string)$request->getUri();
		return $url;
		
    }
	
	function getProofFilewithoutDownload($my_file_name){
        $s3 = new S3Client([
            'credentials' => [ 
                    'key' => $this->access_key, 
                    'secret' => $this->secret 
                ],
            'version' => 'latest',
            'region'  => 'us-east-2'
        ]);

        try {
            //echo getcwd(); die;
            // Get the object.
            $result = $s3->getObject([
                'Bucket' => $bucket,
                'Key'    => $my_file_name,
				'SaveAs' => getcwd().'/assets/s3/a.pdf'
                
            ]);
          
           // echo $result;
        } catch (S3Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }
	
    function listObjectsinBucket(){
        $s3 = new S3Client([
            'credentials' => [ 
                    'key' => $this->access_key, 
                    'secret' => $this->secret 
                ],
            'version' => 'latest',
            'region'  => 'us-east-2'
        ]);

        try {
            // Get the object.
           /* $iterator = $client->getIterator('ListObjects', array(
                    'Bucket' => $bucket,
                    'Prefix' => 'foo'
                ));*/
            $result = $s3->getIterator('ListObjects',[
                'Bucket' => $this->bucket_name,
            ]);

           foreach ($result as $object) {
               echo"<pre>";print_r($object);
                echo $object['Key'] . "\n<br/>";
            }
        } catch (S3Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }
    
    function deleteObjectfile($keyname){
        $s3 = new S3Client([
            'credentials' => [ 
                    'key' => $this->access_key, 
                    'secret' => $this->secret 
                ],
            'version' => 'latest',
            'region'  => 'us-east-2'
        ]);
        try
        {
            //echo 'Attempting to delete ' . $keyname . '...' . PHP_EOL;

            $result = $s3->deleteObject([
                'Bucket' => $this->bucket_name,
                'Key'    => $keyname
            ]);

            return "1";
        }
        catch (S3Exception $e) {
            exit('Error: ' . $e->getAwsErrorMessage() . PHP_EOL);
        }
 
    }
	
	function createFolder($bucket,$mainfolder,$name){
        $s3 = new S3Client([
            'credentials' => [ 
                    'key' => $this->access_key, 
                    'secret' => $this->secret 
                ],
            'version' => 'latest',
            'region'  => 'us-east-2'
        ]);

        try {
			
            // Get the object.
            $result =  $s3->putObject(array( 
                'Bucket' => $bucket,
                'Key'    => $mainfolder."/".$name."/",
               /* 'Body'   => "",
                'ACL'    => 'public-read'*/
            ));
          
            return $result;
        } catch (S3Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }
	
	function upload_fileinFolder($bucket,$file_name,$file_path)
	{
        
		try {       
        $clientS3 = new Aws\S3\S3Client([
          'version' => $this->version,
          'region'  => $this->region,
          //'endpoint' => $this->s3_url,
          'credentials' => [
                    'key'    => $this->access_key,
                    'secret' => $this->secret,
              ],
        ]);

        // putObject method sends data to the chosen bucket (in our case, teste-marcelo)
        $response = $clientS3->putObject(array(
            'Bucket' => $bucket,
            'Key'    => $file_name,
            'SourceFile' => $file_path,
        ));
            if($response['ObjectURL']){
               
                return $response;//die;
            }
            
        } catch(Exception $e) {
            echo "Error > {$e->getMessage()}";
        }
        
	}

    function listObjectsinBucketBatch($bucket, $folder){
        $s3 = new S3Client([
            'credentials' => [ 
                    'key' => $this->access_key, 
                    'secret' => $this->secret 
                ],
            'version' => 'latest',
            'region'  => 'us-east-2'
        ]);

        try {
            // Get the object.
           /* $iterator = $client->getIterator('ListObjects', array(
                    'Bucket' => $bucket,
                    'Prefix' => 'foo'
                ));*/
            $result = $s3->getIterator('ListObjects',[
                'Bucket' => '<Your Bucket Name>',
                'Prefix'=> '<Your Main Folder Name>/'.$bucket.'/'.$folder.'/'
            ]);
            $res = array();
            foreach ($result as $object) {
               $res[] = $object['Key'];
            }
           return $res;
        } catch (S3Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }

}
