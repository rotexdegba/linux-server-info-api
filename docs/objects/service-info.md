[Documentation Home](../index.md) > [/server/services](../server-services.md)

# Service Info

## Object Definition


| Property | Description | Data Type |
| --- | --- | --- |
| name | The name of the service represented by this object. Could be empty if it cannot be retrieved.| String |
| description | The description of the service represented by this object. Could be empty if it cannot be retrieved. | String |
| loaded | Indicates whether or not a service has been loaded. A value of **1** means the service has been loaded. A value of **0** means the service has NOT been loaded. | Integer |
| started | Indicates whether or not a service has been started. A value of **1** means the service has been started. A value of **0** means the service has NOT been started. | Integer |
| state | A description of the current state / status of the service. Could be empty if it cannot be retrieved. | String |
