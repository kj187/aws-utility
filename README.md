
# AWS Utility

**Lightweight AWS Utility**

Author: 
 - [Julian Kleinhans](https://github.com/kj187)

## Description
A lightweight AWS utility that allows you to easily access the Amazon Web Services APIs.
At least the ones that are important to me, so not all methods for the APIs are supported yet.

## Requirements

### Composer 

```
$ curl -sS https://getcomposer.org/installer | php
$ php composer.phar install
```

### AWS access

```
$ export AWS_ACCESS_KEY_ID=YOURACCESSKEY
$ export AWS_SECRET_ACCESS_KEY=YOURSECRETACCESSKEY
```

And make sure this user is allowed to use at least the following actions:

#### Kinesis

- DescribeStream
- GetRecords
- GetShardIterator
- ListStreams
- PutRecord
- PutRecords 

#### API Gateway

- apigateway:GET

## Usage

All commands are interactive, you have to choose a stream/endpoint of a pool of available streams/endpoints.

### Kinesis
To check how many records are available in a stream, use:

```
$ php bin/application.php kinesis:consumer
```

To push data to a stream, use:

```
$ php bin/application.php kinesis:producer
```

But first, create a new directory "mocks" below "resources" and move your data inside. One file equals one record.


### API Gateway

```
$ php bin/application.php api-gateway:producer
```

```
$ php bin/application.php api-gateway:producer --region=eu-west-2
```

```
$ php bin/application.php api-gateway:producer --awsAccessKeyId=AKIQIGRDAXXXX56SEGNA --awsSecretAccessKey=nHCNP8PJyNOiLAF86O1tTrNCC/bT0boBQ+Lm15F
```

```
$ php bin/application.php api-gateway:producer --assumeRole --assumedRoleArn='arn:aws:iam::1234567898765:role/MyRoleName-1D1A0IQS32268' --assumedRoleExternalId='123abc456def789'
```

## Example

![Example](http://res.cloudinary.com/kj187/image/upload/v1466067037/KinesisUtilityExample_pluraf.png)