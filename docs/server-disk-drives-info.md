[Documentation Home](index.md)

# /server/disk-drives-info

This endpoint retrieves and returns all Disk Drive information on the machine this web-application is running on.

**Allowed HTTP Methods:** GET

## Sample Request

### Curl

```
curl --location --request GET "http://your-server.com/server/disk-drives-info?token=<TOKEN_VALUE>"
```

### Javascript Fetch API

```javascript
var requestOptions = {
  method: 'GET',
  redirect: 'follow'
};

fetch("http://your-server.com/server/disk-drives-info?token=<TOKEN_VALUE>", requestOptions)
   .then(response => response.json())
   .then(result => console.log(result))
   .catch(error => console.log('error', error));
```

### Javascript jQuery

```javascript
var settings = {
  "url": "http://your-server.com/server/disk-drives-info?token=<TOKEN_VALUE>",
  "method": "GET",
  "timeout": 0
};

$.ajax(settings).done(function (response) {
  console.log(response);
});
```

### Javascript XHR

```javascript
var xhr = new XMLHttpRequest();

xhr.addEventListener("readystatechange", function() {
  if(this.readyState === 4) {
    console.log(this.responseText);
  }
});

xhr.open("GET", "http://your-server.com/server/disk-drives-info?token=<TOKEN_VALUE>");

xhr.send();
```

### Nodejs

```javascript
var http = require('http');

var options = {
  'method': 'GET',
  'hostname': 'your-server.com',
  'port': 8880, // or whatever port the application is running on, typically 80
  'path': '/server/disk-drives-info?token=<TOKEN_VALUE>',
  'maxRedirects': 20
};

var req = http.request(options, function (res) {
  var chunks = [];

  res.on("data", function (chunk) {
    chunks.push(chunk);
  });

  res.on("end", function (chunk) {
    var body = Buffer.concat(chunks);
    console.log(body.toString());
  });

  res.on("error", function (error) {
    console.error(error);
  });
});

req.end();
```

### PHP

```PHP
$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "http://your-server.com/server/disk-drives-info?token=<TOKEN_VALUE>",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET"
]);

$response = curl_exec($curl);

curl_close($curl);
echo $response;
```

### Python

```python
import http.client
port = 8880 # or whatever port the application is running on, typically 80
conn = http.client.HTTPConnection("your-server.com", port)
payload = ''
conn.request("GET", "/server/disk-drives-info?token=<TOKEN_VALUE>", payload)
res = conn.getresponse()
data = res.read()
print(data.decode("utf-8"))
```

### Ruby

```ruby
require "uri"
require "net/http"

url = URI("http://your-server.com/server/disk-drives-info?token=<TOKEN_VALUE>")
http = Net::HTTP.new(url.host, url.port);
request = Net::HTTP::Get.new(url)

response = http.request(request)
puts response.read_body
```

> **`your-server.com`** should be replaced with the name or IP address of the server this application is running on (the port should also be  appended if different from the standard HTTP and HTTPS ports 80 and 443)

> **`<TOKEN_VALUE>`** should be replaced with an actual token you have generated in the application

> **NOTE:** You will have to tweak these examples to support **HTTPS** if this application is running via **HTTPS**


## Sample Response

```javascript
{
   "status_code":200,
   "status_desc":"Ok",
   "data":[
      {
         "name":"TOSHIBA THNSNJ128G8NU",
         "vendor":"TOSHIBA",
         "device":"\\\\.\\PHYSICALDRIVE1",
         "bytes_read":-1,
         "bytes_written":-1,
         "size_in_bytes":128034708480,
         "partitions":[
            {
               "name":"Disk #1, Partition #0 (GPT: System)",
               "size_in_bytes":314572800
            },
            {
               "name":"Disk #1, Partition #1 (GPT: Basic Data)",
               "size_in_bytes":126575900672
            },
            {
               "name":"Disk #1, Partition #2 (GPT: Unknown)",
               "size_in_bytes":1008730112
            }
         ]
      },
      {
         "name":"HGST HTS721010A9E630",
         "vendor":"HGST",
         "device":"\\\\.\\PHYSICALDRIVE0",
         "bytes_read":-1,
         "bytes_written":-1,
         "size_in_bytes":1000202273280,
         "partitions":[
            {
               "name":"Disk #0, Partition #0 (GPT: Basic Data)",
               "size_in_bytes":980163756032
            },
            {
               "name":"Disk #0, Partition #1 (GPT: Unknown)",
               "size_in_bytes":20039335936
            }
         ]
      }
   ],
   "time_generated":"2020-09-21T11:59:38-06:00"
}
```


## Response Definitions

The following table describes each item in the response.


| Response Item | Description | Data type |
| --- | --- | --- |
| status_code | The HTTP status code for the response. See [here](http-status-codes.md) for definitions. | Integer |
| status_desc | The HTTP status description for the response. See [here](http-status-codes.md) for definitions.| String |
| time_generated | A timestamp (in [ISO 8601](https://www.iso.org/iso-8601-date-and-time-format.html) format)of when the response was generated by the server (i.e. your instance of this web application) | String |
| data | An array of [Disk Drive Info](disk-drive-info.md) objects, each representing information about each disk drive on the system this application is running on | Array |
