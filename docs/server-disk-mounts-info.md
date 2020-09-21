[Documentation Home](index.md)

# /server/disk-mounts-info

This endpoint retrieves and returns all Disk Mount information on the machine this web-application is running on.

**Allowed HTTP Methods:** GET

## Sample Request

### Curl

```
curl --location --request GET "http://your-server.com/server/disk-mounts-info?token=<TOKEN_VALUE>"
```

### Javascript Fetch API

```javascript
var requestOptions = {
  method: 'GET',
  redirect: 'follow'
};

fetch("http://your-server.com/server/disk-mounts-info?token=<TOKEN_VALUE>", requestOptions)
   .then(response => response.json())
   .then(result => console.log(result))
   .catch(error => console.log('error', error));
```

### Javascript jQuery

```javascript
var settings = {
  "url": "http://your-server.com/server/disk-mounts-info?token=<TOKEN_VALUE>",
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

xhr.open("GET", "http://your-server.com/server/disk-mounts-info?token=<TOKEN_VALUE>");

xhr.send();
```

### Nodejs

```javascript
var http = require('http');

var options = {
  'method': 'GET',
  'hostname': 'your-server.com',
  'port': 8880, // or whatever port the application is running on, typically 80
  'path': '/server/disk-mounts-info?token=<TOKEN_VALUE>',
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
  CURLOPT_URL => "http://your-server.com/server/disk-mounts-info?token=<TOKEN_VALUE>",
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
conn.request("GET", "/server/disk-mounts-info?token=<TOKEN_VALUE>", payload)
res = conn.getresponse()
data = res.read()
print(data.decode("utf-8"))
```

### Ruby

```ruby
require "uri"
require "net/http"

url = URI("http://your-server.com/server/disk-mounts-info?token=<TOKEN_VALUE>")
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
         "name":"Data (Fixed drive)",
         "mount_point":"D:\\",
         "type":"NTFS",
         "size_in_bytes":980163751936,
         "used_bytes":792923242496,
         "free_bytes":187240509440,
         "free_percent":19,
         "used_percent":81,
         "options":[
            "automount",
            "indexed"
         ]
      },
      {
         "name":"OS_Install (Fixed drive)",
         "mount_point":"C:\\",
         "type":"NTFS",
         "size_in_bytes":126575898624,
         "used_bytes":98073186304,
         "free_bytes":28502712320,
         "free_percent":23,
         "used_percent":77,
         "options":[
            "automount",
            "boot",
            "indexed"
         ]
      },]
      {
         "name":" (CD-ROM)",
         "mount_point":"E:\\",
         "type":"",
         "size_in_bytes":-1,
         "used_bytes":-1,
         "free_bytes":-1,
         "free_percent":-1,
         "used_percent":-1,
         "options":[
            "automount"
         ]
      }
   ],
   "time_generated":"2020-09-21T14:57:49-06:00"
}
```


## Response Definitions

The following table describes each item in the response.


| Response Item | Description | Data type |
| --- | --- | --- |
| status_code | The HTTP status code for the response. See [here](http-status-codes.md) for definitions. | Integer |
| status_desc | The HTTP status description for the response. See [here](http-status-codes.md) for definitions.| String |
| time_generated | A timestamp (in [ISO 8601](https://www.iso.org/iso-8601-date-and-time-format.html) format)of when the response was generated by the server (i.e. your instance of this web application) | String |
| data | An array of [Disk Mount Info](disk-mount-info.md) objects, each representing information about each disk mount on the system this application is running on | Array |