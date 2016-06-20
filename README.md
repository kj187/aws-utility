
# AWS Utility

**Lightweight AWS Utility**

Author: 
 - [Julian Kleinhans](https://github.com/kj187)

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

For Kinesis:

- DescribeStream
- GetRecords
- GetShardIterator
- ListStreams
- PutRecord
- PutRecords 

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


#### Example

![Example](http://res.cloudinary.com/kj187/image/upload/v1466067037/KinesisUtilityExample_pluraf.png)

### API Gateway

TODO