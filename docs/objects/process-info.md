[Documentation Home](../index.md) > [/server/processes](../server-processes.md)

## Process Info
----

### Object Definition


| Property | Description | Data Type |
| --- | --- | --- |
| name | The name of the process represented by this object. Could be empty if it cannot be retrieved.| String |
| command_line | A string representing the command used to create / start the process represented by this object. Could be empty if it cannot be retrieved. | String |
| num_threads | The number of threads associated with the process represented by this object. A value of **-1** is returned if it cannot be retrieved. | Integer |
| state | A string representing the state of the process represented by this object. Could be empty if it cannot be retrieved. | String |
| memory | The amount of memory in bytes used by the process represented by this object. A value of **-1.0** is returned if it cannot be retrieved. | Float |
| peak_memory | The amount of peak memory in bytes used by the process represented by this object. A value of **-1.0** is returned if it cannot be retrieved. | Float |
| pid | The process ID number of the process represented by this object. A value of **-1** is returned if it cannot be retrieved. | Integer |
| user | The user ID of the operating system user that created and owns the process represented by this object. Could be empty if it cannot be retrieved.| String |
| io_bytes_read | The number of bytes read by the process represented by this object. A value of **-1.0** is returned if it cannot be retrieved. | Float |
| io_bytes_written | The number of bytes written by the process represented by this object. A value of **-1.0** is returned if it cannot be retrieved. | Float |
