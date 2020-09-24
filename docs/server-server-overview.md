[Documentation Home](index.md)

# /server/server-overview

This endpoint retrieves basic system information such as the Operating System name and version, Total RAM, Free RAM, Used RAM, etc on the machine this web-application is running on.

**Allowed HTTP Methods:** GET

## Sample Request

### Curl

```
curl --location --request GET "http://your-server.com/server/server-overview?token=<TOKEN_VALUE>"
```

### Javascript Fetch API

```javascript
var requestOptions = {
  method: 'GET',
  redirect: 'follow'
};

fetch("http://your-server.com/server/server-overview?token=<TOKEN_VALUE>", requestOptions)
   .then(response => response.json())
   .then(result => console.log(result))
   .catch(error => console.log('error', error));
```

### Javascript jQuery

```javascript
var settings = {
  "url": "http://your-server.com/server/server-overview?token=<TOKEN_VALUE>",
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

xhr.open("GET", "http://your-server.com/server/server-overview?token=<TOKEN_VALUE>");

xhr.send();
```

### Nodejs

```javascript
var http = require('http');

var options = {
  'method': 'GET',
  'hostname': 'your-server.com',
  'port': 8880, // or whatever port the application is running on, typically 80
  'path': '/server/server-overview?token=<TOKEN_VALUE>',
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
  CURLOPT_URL => "http://your-server.com/server/server-overview?token=<TOKEN_VALUE>",
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
conn.request("GET", "/server/server-overview?token=<TOKEN_VALUE>", payload)
res = conn.getresponse()
data = res.read()
print(data.decode("utf-8"))
```

### Ruby

```ruby
require "uri"
require "net/http"

url = URI("http://your-server.com/server/server-overview?token=<TOKEN_VALUE>")
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
   "data":{
      "host_name":"my-msi-laptop",
      "os_family":"Windows",
      "kernel_version":"10.0.19041",
      "distro_name":"Microsoft Windows 10 Home",
      "architecture":"AMD64",
      "system_model":"Micro-Star International Co., Ltd. (GE62 2QF)",
      "uptime":1215616,
      "uptime_text":"14 days, 1 hours, 40 minutes, 46 seconds",
      "last_booted_timestamp":1599666750,
      "web_software":"PHP 7.2.31 Development Server",
      "php_version":"7.2.31",
      "virtualization":"",
      "free_ram_bytes":3121774592,
      "free_swap_bytes":-1,
      "total_ram_bytes":17057554432,
      "total_swap_bytes":23549,
      "used_ram_bytes":13935779840,
      "used_swap_bytes":23550,
      "overall_cpu_usage_percent":22,
      "total_num_physical_cpu_cores":4,
      "total_num_virtual_or_logical_processors":4,
      "total_number_of_processes":391,
      "total_number_of_threads":5168,
      "total_number_of_running_processes_linux":-1,
      "total_number_of_sleeping_processes_linux":-1,
      "total_number_of_stopped_processes_linux":-1,
      "total_number_of_zombie_processes_linux":-1,
      "number_of_logged_in_users":1,
      "cpus_info":[
         {
            "cpu_number":0,
            "usage_percentage":60,
            "vendor":"GenuineIntel",
            "model":"Intel(R) Core(TM) i7-5700HQ CPU @ 2.70GHz",
            "speed_mhz":2701
         },
         {
            "cpu_number":1,
            "usage_percentage":60,
            "vendor":"GenuineIntel",
            "model":"Intel(R) Core(TM) i7-5700HQ CPU @ 2.70GHz",
            "speed_mhz":2701
         },
         {
            "cpu_number":2,
            "usage_percentage":60,
            "vendor":"GenuineIntel",
            "model":"Intel(R) Core(TM) i7-5700HQ CPU @ 2.70GHz",
            "speed_mhz":2701
         },
         {
            "cpu_number":3,
            "usage_percentage":60,
            "vendor":"GenuineIntel",
            "model":"Intel(R) Core(TM) i7-5700HQ CPU @ 2.70GHz",
            "speed_mhz":2701
         }
      ],
      "selinux_enabled":-1,
      "selinux_mode":"",
      "selinux_policy":""
   },
   "time_generated":"2020-09-23T11:32:57-06:00"
}
```


## Response Definitions

The following table describes each item in the response.


| Response Item | Description | Data type |
| --- | --- | --- |
| status_code | The HTTP status code for the response. See [here](http-status-codes.md) for definitions. | Integer |
| status_desc | The HTTP status description for the response. See [here](http-status-codes.md) for definitions.| String |
| time_generated | A timestamp (in [ISO 8601](https://www.iso.org/iso-8601-date-and-time-format.html) format)of when the response was generated by the server (i.e. your instance of this web application) | String |
| data | An array of [Server Overview Info](objects/server-overview-info.md) objects, each representing basic system information such as the Operating System name and version, Total RAM, Free RAM, Used RAM, etc on the machine this web-application is running on | Array |
