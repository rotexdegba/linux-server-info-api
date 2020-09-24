[Documentation Home](index.md)

# Supported HTTP Status Codes and Descriptions

| Status Code | Status Description | Long Description |
| --- | --- | --- |
| 200 | OK | Request succeeded and client has been sent the requested data. |
| 401 | Unauthorized | Access denied by the server, request requires a valid token. An expired token or no token was supplied. |
| 403 | Forbidden | Access denied by the server, request requires a valid token that has permission to access specified endpoint. A token that does not have permission to access specified endpoint was supplied. <br><br>AS OF THE INITIAL RELEASE OF THIS APPLICATION, GRANTING EACH TOKEN PERMISSION TO SPECIFIC ENDPOINTS HAS NOT YET BEEN IMPLEMENTED, EACH TOKEN THAT IS NOT EXPIRED, CURRENTLY HAS PERMISSION TO ACCESS ALL ENDPOINTS. |
| 404 | Not Found | The server can not find the requested endpoint. |
| 405 | Method Not Allowed | The request HTTP method is known by the server but has been disabled and cannot be used for the requested endpoint. |
| 429 | Too Many Requests | The user has sent too many requests in a given amount of time ("rate limiting"). Maximum API Requests per Day exceeded for the specified token. |
| 500 | Internal Server Error | The server encountered an unexpected condition which prevented it from fulfilling the request. |
