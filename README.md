# Amazon S3 Library for CodeIgniter (Latest)

Easily integrate your CI applications to Amazon's Simple Storage Solution with this library.

This library is created By Gautam Chawla

# Setup

 Edit config/s3.php with your appropriate settings
 Copy config and library files to your CI installation
 
 #Include Library into controller
 <pre><code>
  // Load Library
  $this->load->library('S3_bucket');
  
  // Upload a file
 $this->s3_bucket->upload_file($destination,$file_name);
  
  // List all objects in bucket
 $this->s3_bucket->listObjectsinBucket();
</code></pre>

# References

* "Amazon S3 Documentation":https://docs.aws.amazon.com/AmazonS3/latest/dev/Introduction.html
