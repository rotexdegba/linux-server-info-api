# /server/cpus-info

This endpoint retrieves and returns all CPU information on the machine this web-application is running on.

**Allowed HTTP Methods:** GET

## Sample Request

### Curl
`curl --location --request GET "http://your-server.com/server/cpus-info?token=<TOKEN_VALUE>"`

### PHP

```PHP
$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "http://your-server.com/server/cpus-info?token=<TOKEN_VALUE>",
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

> **`your-server.com`** should be replaced with the name or IP address of the server this application is running on

> **`<TOKEN_VALUE>`** should be replaced with an actual token you have generated in the application


## Sample Response

```javascript
{
   "status_code":200,
   "status_desc":"Ok",
   "data":[
      {
         "cpu_number":0,
         "usage_percentage":45,
         "vendor":"GenuineIntel",
         "model":"Intel(R) Core(TM) i7-5700HQ CPU @ 2.70GHz",
         "speed_mhz":2701
      },
      {
         "cpu_number":1,
         "usage_percentage":45,
         "vendor":"GenuineIntel",
         "model":"Intel(R) Core(TM) i7-5700HQ CPU @ 2.70GHz",
         "speed_mhz":2701
      },
      {
         "cpu_number":2,
         "usage_percentage":45,
         "vendor":"GenuineIntel",
         "model":"Intel(R) Core(TM) i7-5700HQ CPU @ 2.70GHz",
         "speed_mhz":2701
      },
      {
         "cpu_number":3,
         "usage_percentage":45,
         "vendor":"GenuineIntel",
         "model":"Intel(R) Core(TM) i7-5700HQ CPU @ 2.70GHz",
         "speed_mhz":2701
      },
      {
         "cpu_number":4,
         "usage_percentage":45,
         "vendor":"GenuineIntel",
         "model":"Intel(R) Core(TM) i7-5700HQ CPU @ 2.70GHz",
         "speed_mhz":2701
      },
      {
         "cpu_number":5,
         "usage_percentage":45,
         "vendor":"GenuineIntel",
         "model":"Intel(R) Core(TM) i7-5700HQ CPU @ 2.70GHz",
         "speed_mhz":2701
      },
      {
         "cpu_number":6,
         "usage_percentage":45,
         "vendor":"GenuineIntel",
         "model":"Intel(R) Core(TM) i7-5700HQ CPU @ 2.70GHz",
         "speed_mhz":2701
      },
      {
         "cpu_number":7,
         "usage_percentage":45,
         "vendor":"GenuineIntel",
         "model":"Intel(R) Core(TM) i7-5700HQ CPU @ 2.70GHz",
         "speed_mhz":2701
      }
   ],
   "time_generated":"2020-09-14T17:17:13-06:00"
}
```


## Response Definitions

The following table describes each item in the response.