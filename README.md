
# AWS Utility

[![Build Status](https://travis-ci.org/kj187/aws-utility.svg?branch=master)](https://travis-ci.org/kj187/aws-utility)
[![Code Climate](https://codeclimate.com/github/kj187/aws-utility/badges/gpa.svg)](https://codeclimate.com/github/kj187/aws-utility)
[![Test Coverage](https://codeclimate.com/github/kj187/aws-utility/badges/coverage.svg)](https://codeclimate.com/github/kj187/aws-utility/coverage)
[![Issue Count](https://codeclimate.com/github/kj187/aws-utility/badges/issue_count.svg)](https://codeclimate.com/github/kj187/aws-utility)

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
$ sudo mv composer.phar /usr/local/bin/composer
```

## Installation

```
$ git clone git@github.com:kj187/aws-utility.git
$ cd aws-utility
$ composer install
```

## AWS access

The following example shows how you would configure environment variables:

```
$ export AWS_ACCESS_KEY_ID=YOURACCESSKEY
$ export AWS_SECRET_ACCESS_KEY=YOURSECRETACCESSKEY
```

But, you can also set these keys as command options like:

```
$ php bin/aws-utility.php api-gateway:producer --awsAccessKeyId='YOURACCESSKEY' --awsSecretAccessKey='YOURSECRETACCESSKEY'
```

### Access rights

Make sure that the user you are using have the following actions:

#### Kinesis

- kinesis:DescribeStream
- kinesis:GetRecords
- kinesis:GetShardIterator
- kinesis:ListStreams
- kinesis:PutRecord
- kinesis:PutRecords 

#### API Gateway

- apigateway:GET


## Getting Started

All commands are interactive, you dont need to tell this application what stream or endpoint you want to work with,  
it will ask you, while it shows you all available streams or endpoints as a list. 
Of course, you can also hand over this information as an argument, so that you dont get a question (quite handy for automation).

Keep in mind, all commands have a few optional options, just check it with:
  
```
$ php bin/aws-utility.php <COMMAND> --help
```
  
### Kinesis

#### Consumer

Checks how many records are available in a stream:

```
$ php bin/aws-utility.php kinesis:consumer
```

![Example](http://res.cloudinary.com/kj187/image/upload/v1466664373/aws-utility-example_hcikjs.png)

#### Producer 

Pushes records to a stream:

```
$ php bin/aws-utility.php kinesis:producer
```

But what records? You have to create some first. Below the "resources" directory, create a new directory "mocks/kinesis/".
Here you could add one or multiple JSON files for example, one file is one record and will be pushed to the choosen stream.

### API Gateway

#### Producer

Pushes records to a API Gateway endpoint:

```
$ php bin/aws-utility.php api-gateway:producer
```

Same here, you have to create some records first. Below the "resources" directory, create a new directory "mocks/api-gateway/".
Here you could add one or multiple JSON files for example, one file is one record and will be pushed to the choosen endpoint.


### Assume Role

Returns a set of temporary security credentials (consisting of an access key ID, a secret access key, and a security token) 
that you can use to access AWS resources that you might not normally have access to.

**Important:** You cannot call AssumeRole by using AWS root account credentials; access is denied. 
You must use credentials for an IAM user or an IAM role to call AssumeRole. 

http://docs.aws.amazon.com/STS/latest/APIReference/API_AssumeRole.html

To use all described commands above with AssumeRole, just add some options to the command

| Option                  | Description                                                                                         | Example                                                  |
|-------------------------|-----------------------------------------------------------------------------------------------------|----------------------------------------------------------|
| --assumeRole            | Required to enable the assumeRole feature. You dont need to add a value                             |                                                          |
| ---assumedRoleArn       | The Amazon Resource Name (ARN) of the role that the caller is assuming.                             | arn:aws:iam::1234567898765:role/MyRoleName-1D1A0IQS32268 |
| --assumedRoleExternalId | A unique identifier that is used by third parties when assuming roles in their customers' accounts. | 123abc456def789                                          |

```
$ php bin/aws-utility.php api-gateway:producer --assumeRole --assumedRoleArn='arn:aws:iam::1234567898765:role/MyRoleName-1D1A0IQS32268' --assumedRoleExternalId='123abc456def789'
```
