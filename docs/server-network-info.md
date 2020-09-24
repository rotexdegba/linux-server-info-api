[Documentation Home](index.md)

# /server/network-info

This endpoint retrieves and returns information about all attached network devices on the machine this web-application is running on.

**Allowed HTTP Methods:** GET

## Sample Request

### Curl

```
curl --location --request GET "http://your-server.com/server/network-info?token=<TOKEN_VALUE>"
```

### Javascript Fetch API

```javascript
var requestOptions = {
  method: 'GET',
  redirect: 'follow'
};

fetch("http://your-server.com/server/network-info?token=<TOKEN_VALUE>", requestOptions)
   .then(response => response.json())
   .then(result => console.log(result))
   .catch(error => console.log('error', error));
```

### Javascript jQuery

```javascript
var settings = {
  "url": "http://your-server.com/server/network-info?token=<TOKEN_VALUE>",
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

xhr.open("GET", "http://your-server.com/server/network-info?token=<TOKEN_VALUE>");

xhr.send();
```

### Nodejs

```javascript
var http = require('http');

var options = {
  'method': 'GET',
  'hostname': 'your-server.com',
  'port': 8880, // or whatever port the application is running on, typically 80
  'path': '/server/network-info?token=<TOKEN_VALUE>',
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
  CURLOPT_URL => "http://your-server.com/server/network-info?token=<TOKEN_VALUE>",
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
conn.request("GET", "/server/network-info?token=<TOKEN_VALUE>", payload)
res = conn.getresponse()
data = res.read()
print(data.decode("utf-8"))
```

### Ruby

```ruby
require "uri"
require "net/http"

url = URI("http://your-server.com/server/network-info?token=<TOKEN_VALUE>")
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
         "name":"Intel(R) Dual Band Wireless-AC 3160",
         "speed_bits_per_second":60000,
         "type":"Ethernet 802.3",
         "state":"media disconnected",
         "num_bytes_received":10682,
         "num_received_errors":0,
         "num_received_packets":96,
         "num_bytes_sent":6194,
         "num_sent_errors":0,
         "num_sent_packets":61,
         "gateway":"",
         "ipv4":"",
         "mac":"A4:6E:83:85:23:CE"
      },
      {
         "name":"Killer E2200 Gigabit Ethernet Controller",
         "speed_bits_per_second":1000000000,
         "type":"Ethernet 802.3",
         "state":"up",
         "num_bytes_received":45884334743,
         "num_received_errors":0,
         "num_received_packets":43441932,
         "num_bytes_sent":4146436168,
         "num_sent_errors":0,
         "num_sent_packets":17249783,
         "gateway":"10.0.0.1",
         "ipv4":"10.0.0.123",
         "mac":"F8:GB:8C:81:63:A2"
      },
      {
         "name":"Bluetooth Device (Personal Area Network) #2",
         "speed_bits_per_second":3000000,
         "type":"Ethernet 802.3",
         "state":"media disconnected",
         "num_bytes_received":-1,
         "num_received_errors":-1,
         "num_received_packets":-1,
         "num_bytes_sent":-1,
         "num_sent_errors":-1,
         "num_sent_packets":-1,
         "gateway":"",
         "ipv4":"",
         "mac":""
      }
   ],
   "time_generated":"2020-09-22T17:04:03-06:00"
}
```


## Response Definitions

The following table describes each item in the response.


| Response Item | Description | Data type |
| --- | --- | --- |
| status_code | The HTTP status code for the response. See [here](http-status-codes.md) for definitions. | Integer |
| status_desc | The HTTP status description for the response. See [here](http-status-codes.md) for definitions.| String |
| time_generated | A timestamp (in [ISO 8601](https://www.iso.org/iso-8601-date-and-time-format.html) format)of when the response was generated by the server (i.e. your instance of this web application) | String |
| data | An array of [Network Info](objects/network-info.md) objects, each representing information about each network interface device on the system this application is running on | Array |